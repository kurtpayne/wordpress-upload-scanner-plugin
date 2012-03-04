<?php

if ( !defined( 'ABSPATH') ) {
    exit();
}

/**
 * Upload Scan Plugin
 * @author Kurt Payne
 * @version 1.1
 * @package Upload_Scan_Plugin
 */
class Upload_Scan_Plugin {
	
	/**
	 * Constructor.  Fire off hooks
	 * @return Upload_Scan_Plugin
	 */
	public function __construct() {
		$this->hooks();
	}
	
	/**
	 * Plugin hooks
	 * @return void
	 */
	public function hooks() {
		add_action( 'plugins_loaded', array( $this, 'scan_files' ) );
		if ( is_admin() ) {
			add_action( 'admin_menu', array( $this, 'settings_page' ) );		
			add_action( 'admin_init', array( $this, 'upgrade' ) );
		}
	}
	
	/**
	 * Hook into the admin menu
	 * @return void
	 */
	public function settings_page() {
		add_options_page( 'Upload Scan Options', 'Upload Scan', 'manage_options', 'upload-scan-plugin', array( $this, 'plugin_options' ) );
	}

	/**
	 * Set options
	 * @return void
	 */
	public function plugin_options() {
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		if ( !extension_loaded( 'clamav' ) ) {
			echo '<div class="error"><p>The <a href="http://sourceforge.net/projects/php-clamav/" target="_blank">php-clamav extension</a> was not found.</p></div>';
		}
		if ( !$this->is_exec_enabled() ) {
			echo '<div class="error"><p>The <a href="http://www.php.net/manual/en/function.exec.php" target="_blank">exec</a> function is disabled.</p></div>';			
		}
		if ( isset( $_REQUEST['__action'] ) && 'save' == $_REQUEST['__action'] ) {
			if ( !check_admin_referer( 'upload-scan-save-settings' ) ) {
				wp_die( __( 'You do not have sufficient permissions to access this page.' ) );			
			}
			if ( extension_loaded( 'clamav') ) {
				update_option( 'upload-scan_use_clamav', isset( $_POST['upload_scan_use_clamav'] ) ) ;
			}			
			update_option( 'upload-scan_use_command', isset( $_POST['upload_scan_use_command'] ) ) ;
			update_option( 'upload-scan_command', stripslashes( $_POST['upload_scan_command'] ) );
			update_option( 'upload-scan_onfail_email_admin', isset( $_POST['upload_scan_onfail_email_admin'] ) ) ;
			update_option( 'upload-scan_onfail_quarantine_file', isset( $_POST['upload_scan_onfail_quarantine_file'] ) ) ;
			update_option( 'upload-scan_quarantine_folder', $_POST['upload_scan_quarantine_folder'] );
			update_option( 'upload-scan_onfail_send_406', isset( $_POST['upload_scan_onfail_send_406'] ) ) ;
			update_option( 'upload-scan_onfail_log_message', isset( $_POST['upload_scan_onfail_log_message'] ) );
			update_option( 'upload-scan_onfail_log_file', stripslashes( strip_tags( $_POST['upload_scan_onfail_log_file'] ) ) );
		}
		if ( get_option( 'upload-scan_onfail_log_message' ) && !$this->does_log_file_exist() ) {
			echo '<div class="error"><p>The log file does not exist, or is not writable: ' . get_option( 'upload-scan_onfail_log_file' ) . '</p></div>';
		}
		include_once( UPLOAD_SCAN_PLUGIN_DIR . '/settings.php' );
	}

	/**
	 * Upgrade between different versions
	 * @return void
	 */
	public function upgrade() {

		// Get the current version
		$version = get_option( 'upload-scan_version' );

		// Set default options
		if ( empty( $version ) || version_compare( $version, '1.1' ) < 0 ) {
			update_option( 'upload-scan_use_clamav', false );
			update_option( 'upload-scan_use_command', false );
			update_option( 'upload-scan_command', '' );
			update_option( 'upload-scan_onfail_email_admin', false );
			update_option( 'upload-scan_onfail_quarantine_file', false );
			update_option( 'upload-scan_quarantine_folder', '' );
			update_option( 'upload-scan_onfail_send_406', false );
			update_option( 'upload-scan_onfail_log_message', false );
			update_option( 'upload-scan_onfail_log_file', '' );
			update_option( 'upload-scan_version', '1.1' );
		}
	}

	/**
	 * Activate
	 * Check for exec or clamav, otherwise this plugin is useless
	 * Check for a minimum WordPress version, too
	 */
	public function activate() {
		global $wp_version;
		
		// Version check, only 3.3+
		if ( ! version_compare( $wp_version, '3.3', '>=') ) {
			if ( function_exists('deactivate_plugins') ) {
				deactivate_plugins( UPLOAD_SCAN_PLUGIN_DIR . '/upload-scan.php' );
			}
			die( '<strong>Upload Scan</strong> requires WordPress 3.3 or later' );
		}

		// Check for exec or clamav
		if ( !extension_loaded( 'clamav' ) && !$this->is_exec_enabled() ) {
			if ( function_exists('deactivate_plugins') ) {
				deactivate_plugins( UPLOAD_SCAN_PLUGIN_DIR . '/upload-scan.php' );
			}
			die( '<strong>Upload Scan</strong> requires <a href="http://php-clamav.sourceforge.net/" target="_blank">php-clamav</a> or <a href="http://www.php.net/manual/en/function.exec.php" target="_blank">exec</a>.  Please install php-clamav or enable the exec function.' );
		}
	}
	
	/**
	 * Check to see if the exec function is available
	 * @return bool
	 */
	public function is_exec_enabled() {
		return (
			function_exists( 'exec' ) &&
			is_callable( 'exec' ) &&
			!in_array( 'exec', explode( ',', @ini_get( 'disable_functions' ) ) )
		);
	}
	
	/**
	 * Does the log file exist?
	 * @return bool
	 */
	public function does_log_file_exist() {
		$path = get_option( 'upload-scan_onfail_log_file' );
		if ( file_exists( $path ) && !is_writable( $path ) ) {
			return false;
		} else {
			@touch( $path );
			if ( !file_exists( $path) ) {
				return false;
			}
		}
		return true;
	}
	
	/**
	 * Scan files according to user settings
	 * @return void
	 */
	public function scan_files() {

		// Don't scan if there aren't any uploaded files
		if ( empty( $_FILES ) ) {
			return;
		}
		
		// Initialize
		$pass = true;
		require_once( UPLOAD_SCAN_PLUGIN_DIR . '/class-upload-scan-report.php' );
		require_once( UPLOAD_SCAN_PLUGIN_DIR . '/class-upload-scan-report-file.php' );
		require_once( UPLOAD_SCAN_PLUGIN_DIR . '/class-upload-scan-report-printer.php' );
		require_once( UPLOAD_SCAN_PLUGIN_DIR . '/class-upload-scan-report-printer-log.php' );
		$report = new Upload_Scan_Report();
		
		// Clam scan
		if ( extension_loaded( 'clamav' ) && get_option( 'upload-scan_use_clamav' ) ) {
			foreach ( $_FILES as $k => $v ) {
				$file = new Upload_Scan_Report_File();
				$file->setFile( $v );
				$ret = cl_scanfile( $file->tmp_name, $virusname );
				if ( CL_VIRUS == $ret ) {
					$pass = false;
					$file->addMessage( cl_pretcode( $ret ) . " - $virusname" );
					$file->fail = true;
				} else {
					$file->addMessage( cl_pretcode( $ret ) );
				}
				$report->addFile( $file );
			}
		}

		// Run any user defined commands
		if ( get_option('upload-scan_use_command') ) {
			foreach ( $_FILES as $k => $v ) {
				$file = new Upload_Scan_Report_File();
				$file->setFile( $v );
				$cmd = sprintf('UPLOAD_SCANNER_ORIG_FILENAME=%s; UPLOAD_SCANNER_ORIG_TEMPNAME=%s; UPLOAD_SCANNER_ORIG_FILESIZE=%s; UPLOAD_SCANNER_ORIG_FILETYPE=%s; %s',
					escapeshellarg( $v['name'] ),
					escapeshellarg( $v['tmp_name'] ),
					escapeshellarg( $v['size'] ),
					escapeshellarg( $v['type'] ),
					get_option( 'upload-scan_command' )
				);
				
				$tmp = exec( $cmd, $output, $ret );
				$file->addMessage( "Command: $cmd" );
				$file->addMessage( 'Output: ' . implode( "\n", $output ) );
				$file->addMessage( 'Return Code: ' . $ret );
				if ( $ret === 1 ) {
					$pass = false;
					$file->fail = true;
				}
				$report->addFile( $file );
			}
		}

		// Take any user defined actions
		if ( !$pass ) {			
			
			// Quarantine file
			if ( get_option( 'upload-scan_onfail_quarantine_file' ) ) {
				$folder = get_option( 'upload-scan_quarantine_folder' );
				if ( file_exists( $folder ) && is_dir( $folder ) && is_writable( $folder ) ) {
					foreach ( $report->getFiles() as $file ) {
						if ( $file->fail ) {
							$dest = $folder . DIRECTORY_SEPARATOR . $_FILES[$file]['name'] . '.quarantined-' . substr( md5( uniqid() ), -8 );
							move_uploaded_file( $_FILES[$file]['tmp_name'], $dest );
							$file->addMessage("Quaranted to $dest");
						}
					}
				}
			}

			// Send 406
			if ( get_option( 'upload-scan_onfail_send_406' ) ) {
				$report->addMessage( 'Sending 406 and stopping execution' );
			}

			// Report printer adapter
			$log_adapter = new Upload_Scan_Report_Printer_Log();
			
			// Email admin
			if ( get_option( 'upload-scan_onfail_email_admin' ) ) {
				$report->addMessage( 'Emailing ' . get_bloginfo( 'admin_email' ) );
				$ret = wp_mail( get_bloginfo( 'admin_email' ), 'Upload Scan Report', $report->getReport( $log_adapter ) );
			}

			// Log it
			if ( $this->does_log_file_exist() ) {
				file_put_contents( get_option( 'upload-scan_onfail_log_file' ), $report->getReport( $log_adapter ) );
			}
			
			// Send 406
			if ( get_option( 'upload-scan_onfail_send_406' ) ) {
				if ( 'apache2handler' == php_sapi_name() ) {
					header('HTTP/1.0 406 Not Acceptable');
				} else {
					header('Status: 406 Not Acceptable');
				}
				exit('Not acceptable');
			}
		}
	}
}
<?php

if ( !defined( 'ABSPATH') ) {
    exit();
}

/**
 * Upload Scan Plugin
 * @author Kurt Payne
 * @version 1.1
 * @package Upload_Scanner_Plugin
 */
class Upload_Scanner_Plugin {
	
	/**
	 * Constructor.  Fire off hooks
	 * @return upload_scanner_Plugin
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
		add_options_page( 'Upload Scanner Options', 'Upload Scanner', 'manage_options', 'upload-scanner-plugin', array( $this, 'plugin_options' ) );
	}

	/**
	 * Set options
	 * @return void
	 */
	public function plugin_options() {
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		
		// Read the log file
		if ( isset( $_REQUEST['__action'] ) && 'view-log' == $_REQUEST['__action'] ) {
			include_once( UPLOAD_SCANNER_PLUGIN_DIR . '/view-log.php' );

		// Standard settings page
		} else {
			if ( !extension_loaded( 'clamav' ) ) {
				echo '<div class="error"><p>The <a href="http://sourceforge.net/projects/php-clamav/" target="_blank">php-clamav extension</a> was not found.</p></div>';
			}
			if ( !$this->is_exec_enabled() ) {
				echo '<div class="error"><p>The <a href="http://www.php.net/manual/en/function.exec.php" target="_blank">exec</a> function is disabled.</p></div>';			
			}
			
			// Save settings
			if ( isset( $_REQUEST['__action'] ) && 'save' == $_REQUEST['__action'] ) {
				if ( !check_admin_referer( 'upload-scanner-save-settings' ) ) {
					wp_die( __( 'You do not have sufficient permissions to access this page.' ) );			
				}
				if ( extension_loaded( 'clamav') ) {
					update_option( 'upload-scanner_use_clamav', isset( $_POST['upload_scanner_use_clamav'] ) ) ;
				}			
				update_option( 'upload-scanner_use_command', isset( $_POST['upload_scanner_use_command'] ) ) ;
				update_option( 'upload-scanner_command', stripslashes( $_POST['upload_scanner_command'] ) );
				update_option( 'upload-scanner_onfail_email_admin', isset( $_POST['upload_scanner_onfail_email_admin'] ) ) ;
				update_option( 'upload-scanner_onfail_email', $_POST['upload_scanner_onfail_email'] );
				update_option( 'upload-scanner_onfail_quarantine_file', isset( $_POST['upload_scanner_onfail_quarantine_file'] ) ) ;
				update_option( 'upload-scanner_quarantine_folder', $_POST['upload_scanner_quarantine_folder'] );
				update_option( 'upload-scanner_onfail_send_406', isset( $_POST['upload_scanner_onfail_send_406'] ) ) ;
				update_option( 'upload-scanner_onfail_log_message', isset( $_POST['upload_scanner_onfail_log_message'] ) );
				update_option( 'upload-scanner_onfail_log_file', stripslashes( strip_tags( $_POST['upload_scanner_onfail_log_file'] ) ) );
			}
			if ( get_option( 'upload-scanner_onfail_log_message' ) && !$this->does_log_file_exist() ) {
				echo '<div class="error"><p>The log file does not exist, or is not writable: ' . get_option( 'upload-scanner_onfail_log_file' ) . '</p></div>';
			}
			include_once( UPLOAD_SCANNER_PLUGIN_DIR . '/settings.php' );
		}
	}

	/**
	 * Upgrade between different versions
	 * @return void
	 */
	public function upgrade() {

		// Get the current version
		$version = get_option( 'upload-scanner_version' );

		// Set default options
		if ( empty( $version ) || version_compare( $version, '1.1' ) < 0 ) {
			update_option( 'upload-scanner_use_clamav', false );
			update_option( 'upload-scanner_use_command', false );
			update_option( 'upload-scanner_command', '' );
			update_option( 'upload-scanner_onfail_email_admin', false );
			update_option( 'upload-scanner_onfail_email', '' );
			update_option( 'upload-scanner_onfail_quarantine_file', false );
			update_option( 'upload-scanner_quarantine_folder', '' );
			update_option( 'upload-scanner_onfail_send_406', false );
			update_option( 'upload-scanner_onfail_log_message', false );
			update_option( 'upload-scanner_onfail_log_file', '' );
			update_option( 'upload-scanner_version', '1.1' );
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
				deactivate_plugins( UPLOAD_SCANNER_PLUGIN_DIR . '/upload-scan.php' );
			}
			die( '<strong>Upload Scan</strong> requires WordPress 3.3 or later' );
		}

		// Check for exec or clamav
		if ( !extension_loaded( 'clamav' ) && !$this->is_exec_enabled() ) {
			if ( function_exists('deactivate_plugins') ) {
				deactivate_plugins( UPLOAD_SCANNER_PLUGIN_DIR . '/upload-scan.php' );
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
		$path = get_option( 'upload-scanner_onfail_log_file' );
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
		require_once( UPLOAD_SCANNER_PLUGIN_DIR . '/class-upload-scanner-report.php' );
		require_once( UPLOAD_SCANNER_PLUGIN_DIR . '/class-upload-scanner-report-file.php' );
		require_once( UPLOAD_SCANNER_PLUGIN_DIR . '/class-upload-scanner-report-printer.php' );
		require_once( UPLOAD_SCANNER_PLUGIN_DIR . '/class-upload-scanner-report-printer-log.php' );
		require_once( UPLOAD_SCANNER_PLUGIN_DIR . '/class-upload-scanner-report-printer-email.php' );
		$report = new Upload_Scanner_Report();
		
		// Scan $_FILES
		foreach ( $_FILES as $_file ) {

			$file = new Upload_Scanner_Report_File();
			$file->setFile( $_file );
			$report->addFile( $file );

			// Clam scan
			if ( extension_loaded( 'clamav' ) && get_option( 'upload-scanner_use_clamav' ) ) {
				$file->addMessage( 'Scanning with ClamAV' );
				$ret = cl_scanfile( $file->tmp_name, $virusname );
				if ( CL_VIRUS == $ret ) {
					$pass = false;
					$file->addMessage( cl_pretcode( $ret ) . " - $virusname" );
					$file->fail = true;
				} else {
					$file->addMessage( cl_pretcode( $ret ) );
				}
			}

			
			// Run any user defined commands
			if ( get_option('upload-scanner_use_command') ) {
				$cmd = sprintf('UPLOAD_SCANNER_ORIG_FILENAME=%s; UPLOAD_SCANNER_ORIG_TEMPNAME=%s; UPLOAD_SCANNER_ORIG_FILESIZE=%s; UPLOAD_SCANNER_ORIG_FILETYPE=%s; %s',
					escapeshellarg( $file->name ),
					escapeshellarg( $file->tmp_name ),
					escapeshellarg( $file->size ),
					escapeshellarg( $file->type ),
					get_option( 'upload-scanner_command' )
				);
				$tmp = exec( $cmd, $output, $ret );
				$file->addMessage( "Command: $cmd" );
				$file->addMessage( 'Output: ' . implode( "\n", $output ) );
				$file->addMessage( 'Return Code: ' . $ret );
				if ( $ret === 1 ) {
					$pass = false;
					$file->fail = true;
				}
			}
		}

		// Take any user defined actions
		if ( !$pass ) {			
			
			// Quarantine file
			if ( get_option( 'upload-scanner_onfail_quarantine_file' ) ) {
				$folder = get_option( 'upload-scanner_quarantine_folder' );
				if ( file_exists( $folder ) && is_dir( $folder ) && is_writable( $folder ) ) {
					foreach ( $report->getFiles() as $file ) {
						if ( $file->fail ) {
							$dest = $folder . DIRECTORY_SEPARATOR . $file->name . '.quarantined-' . substr( md5( uniqid() ), -8 );
							move_uploaded_file( $file->tmp_name, $dest );
							$file->addMessage("Quarantined to $dest");
						}
					}
				}
			}

			// Send 406
			if ( get_option( 'upload-scanner_onfail_send_406' ) ) {
				$report->addMessage( 'Sending 406 and stopping execution' );
			}

			// Report printer adapter
			$log_adapter   = new Upload_Scanner_Report_Printer_Log();
			$email_adapter = new Upload_Scanner_Report_Printer_Email();

			// Email admin
			if ( get_option( 'upload-scanner_onfail_email_admin' ) ) {
				$report->addMessage( 'Emailing ' . get_option( 'upload-scanner_onfail_email' ) );
				$ret = wp_mail( get_option( 'upload-scanner_onfail_email' ), '[' . get_bloginfo( 'name' ) . '] Upload Scan Report', $report->getReport( $email_adapter ) );
			}

			// Log it
			if ( $this->does_log_file_exist() ) {
				file_put_contents( get_option( 'upload-scanner_onfail_log_file' ), $report->getReport( $log_adapter ), FILE_APPEND );
			}
			
			// Send 406
			if ( get_option( 'upload-scanner_onfail_send_406' ) ) {
				if ( 'apache2handler' == php_sapi_name() ) {
					header('HTTP/1.0 406 Not Acceptable');
				} else {
					header('Status: 406 Not Acceptable');
				}
				require_once( UPLOAD_SCANNER_PLUGIN_DIR .'/406.php' );
				exit();
			}
		}
	}
}
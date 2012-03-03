<?php

if ( !defined( 'ABSPATH') ) {
    exit();
}

/**
 * Upload Scan Plugin
 * @author Kurt Payne
 * @version 1.0
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
		if ( empty( $version ) || version_compare( $version, '1.0' ) < 0 ) {
			update_option( 'upload-scan_use_clamav', false );
			update_option( 'upload-scan_use_command', false );
			update_option( 'upload-scan_command', '' );
			update_option( 'upload-scan_onfail_email_admin', false );
			update_option( 'upload-scan_onfail_quarantine_file', false );
			update_option( 'upload-scan_quarantine_folder', '' );
			update_option( 'upload-scan_onfail_send_406', false );
			update_option( 'upload-scan_version', '1.0' );
		}
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
		$failed = array();
		$report = '';
		
		// Clam scan
		if ( extension_loaded( 'clamav' ) && get_option( 'upload-scan_use_clamav' ) ) {
			foreach ( $_FILES as $k => $v ) {
				$report .= 'Scanning ' . $v['name'] . "\n";
				$report .= "--------------------------------\n";
				$ret = cl_scanfile( $v['tmp_name'], $virusname );
				if ( CL_VIRUS == $ret ) {
					$failed[] = $k;
					$pass = false;
					$report .= cl_pretcode($ret) . " - $virusname\n";
				} else {
					$report .= cl_pretcode($ret) . "\n";
				}
				$report .= "\n\n";
			}
		}

		// Run any user defined commands
		if ( get_option('upload-scan_use_command') ) {
			foreach ( $_FILES as $k => $v ) {
				$report .= 'Scanning ' . $v['name'] . "\n";
				$report .= "--------------------------------\n";
				$cmd = sprintf('UPLOAD_SCANNER_ORIG_FILENAME=%s; UPLOAD_SCANNER_ORIG_TEMPNAME=%s; UPLOAD_SCANNER_ORIG_FILESIZE=%s; UPLOAD_SCANNER_ORIG_FILETYPE=%s; %s',
					escapeshellarg( $v['name'] ),
					escapeshellarg( $v['tmp_name'] ),
					escapeshellarg( $v['size'] ),
					escapeshellarg( $v['type'] ),
					get_option( 'upload-scan_command' )
				);
				
				$tmp = exec( $cmd, $output, $ret );
				$report .= "Running command:\n";
				$report .= "$cmd\n";
				$report .= implode( "\n", $output );
				if ( $ret === 1 ) {
					$failed[] = $k;
					$pass = false;
				}
				$report .= "\n\n";
			}
		}

		// Take any user defined actions
		if ( !$pass ) {			
			
			// Quarantine file
			if ( get_option( 'upload-scan_onfail_quarantine_file' ) ) {
				$folder = get_option( 'upload-scan_quarantine_folder' );
				if ( file_exists( $folder ) && is_dir( $folder ) && is_writable( $folder ) ) {
					$report .= "Quarantined files:\n";
					$report .= "--------------------------------\n";
					foreach ( $failed as $file ) {
						$dest = $folder . DIRECTORY_SEPARATOR . $_FILES[$file]['name'] . '.quarantined-' . substr( md5( uniqid() ), -8 );
						move_uploaded_file( $_FILES[$file]['tmp_name'], $dest );
						$report .= "$dest\n";
					}
					$report .= "\n\n";
				}
			}

			// Email admin
			if ( get_option( 'upload-scan_onfail_email_admin' ) ) {
				$ret = wp_mail( get_bloginfo( 'admin_email' ), 'Upload Scan Report', $report );
			}

			// Send 406
			if ( get_option( 'upload-scan_onfail_send_406' ) ) {
				if ( 'apache2handler' == php_sapi_name() ) {
					header('HTTP/1.0 406 Not Acceptable');
				} else {
					header('Status: 406 Not Acceptable');
				}
				exit();
			}
		}
	}
}
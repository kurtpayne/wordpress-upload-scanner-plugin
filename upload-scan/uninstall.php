<?php

if( !defined( 'ABSPATH') && !defined('WP_UNINSTALL_PLUGIN') )
    exit();
 
delete_option( 'upload-scan_use_clamav' );
delete_option( 'upload-scan_use_command' );
delete_option( 'upload-scan_command' );
delete_option( 'upload-scan_onfail_email_admin' );
delete_option( 'upload-scan_onfail_quarantine_file' );
delete_option( 'upload-scan_quarantine_folder' );
delete_option( 'upload-scan_onfail_send_406' );
delete_option( 'upload-scan_version' );
delete_option( 'upload-scan_onfail_email' );
delete_option( 'upload-scan_onfail_log_message' );
delete_option( 'upload-scan_onfail_log_file' );

<?php

if( !defined( 'ABSPATH') && !defined('WP_UNINSTALL_PLUGIN') )
    exit();

delete_site_option( 'upload-scanner_use_clamav' );
delete_site_option( 'upload-scanner_use_command' );
delete_site_option( 'upload-scanner_command' );
delete_site_option( 'upload-scanner_onfail_email_admin' );
delete_site_option( 'upload-scanner_onfail_quarantine_file' );
delete_site_option( 'upload-scanner_quarantine_folder' );
delete_site_option( 'upload-scanner_onfail_send_406' );
delete_site_option( 'upload-scanner_version' );
delete_site_option( 'upload-scanner_onfail_email' );
delete_site_option( 'upload-scanner_onfail_log_message' );
delete_site_option( 'upload-scanner_onfail_log_file' );

// Delete 1.1 and lower options that may be hanging around
if ( is_multisite() ) {
    delete_option( 'upload-scanner_use_clamav' );
    delete_option( 'upload-scanner_use_command' );
    delete_option( 'upload-scanner_command' );
    delete_option( 'upload-scanner_onfail_email_admin' );
    delete_option( 'upload-scanner_onfail_quarantine_file' );
    delete_option( 'upload-scanner_quarantine_folder' );
    delete_option( 'upload-scanner_onfail_send_406' );
    delete_option( 'upload-scanner_version' );
    delete_option( 'upload-scanner_onfail_email' );
    delete_option( 'upload-scanner_onfail_log_message' );
    delete_option( 'upload-scanner_onfail_log_file' );
}

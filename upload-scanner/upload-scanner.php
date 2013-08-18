<?php
/*
Plugin Name: Upload Scanner
Plugin URI: https://github.com/kurtpayne/wordpress-upload-scanner-plugin
Description: Scan all uploaded files with Clam AV or any arbitrary command.
Version: 1.3
Author: Kurt Payne
Author URI: http://kpayne.me/
License: GPL2
*/

define( 'UPLOAD_SCANNER_PLUGIN_DIR', dirname( realpath( __FILE__ ) ) );
load_plugin_textdomain( 'upload-scanner', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
require_once( UPLOAD_SCANNER_PLUGIN_DIR . '/class-upload-scanner-plugin.php' );
$upload_scanner_plugin = new Upload_Scanner_Plugin();
register_activation_hook( __FILE__ , array( $upload_scanner_plugin, 'activate' ) );

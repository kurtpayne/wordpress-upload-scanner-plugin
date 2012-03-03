<?php
/*
Plugin Name: Upload Scan
Plugin URI: https://github.com/kurtpayne/WordPress-Upload-Scanner-Plugin
Description: Scan all uploaded files with Clam AV or any arbitrary command.
Version: 1.0
Author: Kurt Payne
Author URI: http://kpayne.me/
License: GPL2
*/

define( 'UPLOAD_SCAN_PLUGIN_DIR', dirname( realpath( __FILE__ ) ) );
require_once( UPLOAD_SCAN_PLUGIN_DIR . '/class-upload-scan-plugin.php' );
$upload_scan_plugin = new Upload_Scan_Plugin();

<?php

if ( !defined( 'ABSPATH') ) {
    exit();
}

/**
 * Upload Scan Report
 * @author Kurt Payne
 * @version 1.1
 * @package Upload_Scanner_Plugin
 */
class Upload_Scanner_Report_Printer_Log extends Upload_Scanner_Report_Printer {

	/**
	 * Print the report
	 * @return string
	 */
	public function __toString() {
		$report  = '';
		$report .= '[' . date('Y-m-d H:i:s') . '] ' . __( 'Upload scan:', 'upload_scanner' ) . PHP_EOL;
		$report .= '-------------------------------------------------------'     . PHP_EOL;
		$report .= 'REQUEST_URI:     ' . $this->getCurrentURL()                  . PHP_EOL;
		$report .= 'REMOTE_ADDR:     ' . $_SERVER['REMOTE_ADDR']                 . PHP_EOL;
		$report .= 'SCRIPT_FILENAME: ' . $_SERVER['SCRIPT_FILENAME']             . PHP_EOL;
		$report .= '-------------------------------------------------------'     . PHP_EOL;
		if ( count( $this->_report->getMessages() ) ) :
		$report .= __( 'Messages:', 'upload-scanner' )                           . PHP_EOL;
		foreach ( $this->_report->getMessages() as $message ) :
		$report .= $message                                                      . PHP_EOL;
		endforeach;
		$report .= '-------------------------------------------------------'     . PHP_EOL;
		endif;
		$report .= __( 'Scanned files:', 'upload-scanner' )                      . PHP_EOL;
		$report .= '-------------------------------------------------------'     . PHP_EOL;
		foreach ( $this->_report->getFiles() as $file ) :
		$report .= __( 'Original name:', 'upload-scanner' ) . '  ' . $file->name . PHP_EOL;
		$report .= __( 'Type:', 'upload-scanner' ) . '  ' . $file->type          . PHP_EOL;
		$report .= __( 'Size:', 'upload-scanner' ) . '  '  . $file->size         . PHP_EOL;
		foreach ( $file->getMessages() as $message ) :
		$report .= $message                                                      . PHP_EOL;
		endforeach;
		$report .= '-------------------------------------------------------'     . PHP_EOL;
		endforeach;
		$report .= PHP_EOL . PHP_EOL;
		return $report;
	}
}

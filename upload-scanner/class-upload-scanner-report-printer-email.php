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
class Upload_Scanner_Report_Printer_Email extends Upload_Scanner_Report_Printer {

	/**
	 * Print the report
	 * @return string
	 */
	public function __toString() {
		$report  = '';
		$report .= __( 'Date:', 'upload-scanner' ) . ' ' . date_i18n( get_option( 'date_format' ) ) . PHP_EOL;
		$report .= __( 'Time:', 'upload-scanner' ) . ' ' . date_i18n( get_option( 'time_format' ) ) . PHP_EOL;
		$report .= __( 'IP:', 'upload-scanner' )  . ' ' . $_SERVER['REMOTE_ADDR'] . PHP_EOL;
		$report .= __( 'URL:', 'upload-scanner' ) . ' ' . $this->getCurrentURL() . PHP_EOL;
		$report .=                                                PHP_EOL;
		if ( count( $this->_report->getMessages() ) ) :
		$report .= __( 'Messages:', 'upload-scanner' ) . PHP_EOL;
		$report .= PHP_EOL;
		foreach ( $this->_report->getMessages() as $message ) :
		$report .= __( ' * ', 'upload-scanner' ) . $message . PHP_EOL;
		endforeach;
		$report .= PHP_EOL;
		endif;
		$report .= __( 'Scanned files:', 'upload-scanner' ) . PHP_EOL;
		$report .= PHP_EOL;
		foreach ( $this->_report->getFiles() as $file ) :
		$report .= __( ' + Original name:', 'upload-scanner' ) . ' ' . $file->name . PHP_EOL;
		$report .= __( ' + Type:', 'upload-scanner' ) . ' ' . $file->type . PHP_EOL;
		$report .= __( ' + Size:', 'upload-scanner' ) . ' ' . $file->size . PHP_EOL;
		if ( count( $file->getMessages() ) > 0 ) :
		$report .= __( ' + Messages:', 'upload-scanner' ) . PHP_EOL;
		foreach ( $file->getMessages() as $message ) :
		$report .= __( ' * ', 'upload-scanner' ) . $message . PHP_EOL;
		endforeach;
		endif;
		$report .= PHP_EOL;
		endforeach;
		$report .= PHP_EOL;
		return $report;
	}
}

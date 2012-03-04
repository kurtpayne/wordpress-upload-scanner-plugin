<?php

if ( !defined( 'ABSPATH') ) {
    exit();
}

/**
 * Upload Scan Report
 * @author Kurt Payne
 * @version 1.1
 * @package Upload_Scan_Plugin
 */
abstract class Upload_Scan_Report_Printer {
	
	/**
	 * Upload scan report
	 * @var Upload_Scan_Report
	 */
	protected $_report = null;

	/**
	 * Mutator for scan report
	 * @param Upload_Scan_Report $report
	 */
	public function setReport( Upload_Scan_Report $report ) {
		$this->_report = $report;
	}
}

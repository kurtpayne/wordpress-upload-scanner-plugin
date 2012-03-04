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
class Upload_Scan_Report {
	
	/**
	 * List of files contained in the report
	 * @var array
	 */
	protected $_files = array();

	/**
	 * Messages to include with the report
	 * @var array
	 */
	protected $_messages = array();

	/**
	 * Accessor for messages
	 * @return string[]
	 */
	public function getMessages() {
		return $this->_messages;
	}

	/**
	 * Accessor for files
	 * @return Upload_Scan_Report_File[]
	 */
	public function getFiles() {
		return $this->_files;
	}

	/**
	 * Return a string representation of the report
	 * @param Upload_Scan_Report_Printer $adapter
	 * @return string
	 */
	public function getReport( Upload_Scan_Report_Printer $adapter ) {
		$adapter->setReport( $this );
		return $adapter->__toString();
	}
	
	/**
	 * Add a message to the report
	 * @param string
	 */
	public function addMessage( $message ) {
		$this->_messages[] = $message;
	}
	
	/**
	 * Add a file to the report
	 * @param Upload_Scan_Report_File $file
	 */
	public function addFile( Upload_Scan_Report_File $file ) {
		$this->_files[] = $file;
	}
}

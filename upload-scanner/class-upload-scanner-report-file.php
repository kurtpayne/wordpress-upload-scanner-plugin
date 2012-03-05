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
class Upload_Scanner_Report_File {
	
	/**
	 * Original file name
	 * @var string
	 */
	public $name = '';
	
	/**
	 * Uploaded location
	 * @var string
	 */
	public $tmp_name = '';

	/**
	 * Original type
	 * @var string
	 */
	public $type = '';

	/**
	 * Original size
	 * @var string
	 */
	public $size = '';

	/**
	 * Did the file fail scanning?
	 * @var boolean
	 */
	public $fail = false;
	
	/**
	 * Messages attached to this file
	 * @var array
	 */
	protected $_messages = array();

	/**
	 * Set a copy of the information from $_FILES
	 * @param array $file 
	 */
	public function setFile( array $file ) {
		$this->name     = $file['name'];
		$this->tmp_name = $file['tmp_name'];
		$this->type     = $file['type'];
		$this->size     = $file['size'];
	}
	
	/**
	 * Get messages
	 * @return array
	 */
	public function getMessages() {
		return $this->_messages;
	}
	
	/**
	 * Add a message (e.g. "Scan complete - no viruses found")
	 * @param string $message 
	 */
	public function addMessage( $message ) {
		$this->_messages[] = $message;
	}
}

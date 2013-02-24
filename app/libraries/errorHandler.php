<?php
/**
* DMyers Super Simple MVC
*
* @package    error handler for SSMVC
* @language   PHP
* @author     Don Myers
* @copyright  Copyright (c) 2011
* @license    Released under the MIT License.
*/

class errorHandler {

	public function __construct() {
		set_exception_handler(array($this,'exceptionHandler'));
		set_error_handler(array($this,'oldSchoolErrorHandler'),error_reporting());
	}

	public function exceptionHandler($exception) {
		if (!headers_sent()) {
			header('HTTP/1.0 404 Not Found');
		}

		echo '<pre>';
		print_r($exception);
	}

	/* wrapper old school error handler into new error handler */
	public function oldSchoolErrorHandler($errno, $errstr, $errfile, $errline) {
		$e = new ErrorException($errstr,$errno,0,$errfile,$errline);
		$this->exceptionHandler($e);
		return true;
	}

}

<?php
/**
	* DMyers Super Simple MVC
	*
	* @package    Bootstrap File
	* @language   PHP
	* @author     Don Myers
	* @copyright  Copyright (c) 2011
	* @license    Released under the MIT License.
	*
	*/

class mainController extends baseController {
	public $details = array(
		'version' => '1.0',
		'description' => '',
		'help' => 'http://www.help.com/mainController',
		'type' => 'function'
	);

	public function indexAction() {
		$this->checkAccess();
	
		/* default describe all */
		$here = __DIR__;
		$controllers = glob($here.'/*.php');
		
		foreach ($controllers as $path) {
			$filename = basename($path,'.php');
			
			if (substr($filename,0,4) !== 'base') {
				$fullname = $filename.'Controller';
				$c = new $fullname;
				self::$view->set($filename,$c->describe());
			}
		}

		self::$view->ok();
	}

} /* end controller */

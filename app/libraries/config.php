<?php
/**
* DMyers Super Simple MVC
*
* @package    Config for SSMVC
* @language   PHP
* @author     Don Myers
* @copyright  Copyright (c) 2011
* @license    Released under the MIT License.
*/

class config {
	public static $data = array();
	public static $path;
	public static $folder;

	public function __construct($path=NULL,$folder=NULL) {
		if ($path) {
			self::$path = $path;
			self::$folder = ($folder) ? $folder.'/' : '' ;
		}
	}

	public function get($name,$key,$default=null) {
		$array = $this->read($name);
		if (isset($array[$key])) {
			return $array[$key];
		} else {
			return $default;
		}
	}

	public function read($name) {
		if (isset(self::$data[$name])) {
			return self::$data[$name];
		}

		/* manually load file so $config variable is local */
		$file = self::$path.self::$folder.$name.'.php';

		if (is_file($file)) {
			include($file);
			self::$data[$name] = $config;

			return $config;
		}
	}

}

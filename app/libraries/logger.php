<?php
/**
* DMyers Super Simple MVC
*
* @package    Logger for SSMVC
* @language   PHP
* @author     Don Myers
* @copyright  Copyright (c) 2011
* @license    Released under the MIT License.
*/

class logger {
	public static $path;
	public static $config;
	public static $stamp;
	public static $filestamp;

	public function __construct($path=null,$config=null) {
		if ($path) {
			self::$path = $path;
			self::$stamp = $config->get(get_class($this),'stamp','Y-m-d H:i:s');
			self::$filestamp = $config->get(get_class($this),'filestamp','Y-m-d');

			if (!is_dir(self::$path)) {
				mkdir(self::$path, 0777, true);
			}
		}
	}

	public static function _($msg,$name='log') {
		if ($log_handle = @fopen(self::$path.$name.' '.date(self::$filestamp).'.log','a')) {
			fwrite($log_handle,date(self::$stamp).' '.$msg.chr(10));
			fclose($log_handle);
		}
	}

}

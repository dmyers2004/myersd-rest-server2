<?php
/**
* DMyers Super Simple MVC
*
* @package    Cache for SSMVC
* @language   PHP
* @author     Don Myers
* @copyright  Copyright (c) 2011
* @license    Released under the MIT License.
*/

class cache {
	public static $time;
	public static $path;

	public function __construct($path=null,$config=null) {
		if ($path) {
			self::$path = $path;
			self::$time = $config->get(get_class($this),'time',3600);

			if (!is_dir(self::$path)) {
				mkdir(self::$path, 0777, true);
			}
		}
	}

	/* cache functions */
	public function __get($key) {
    $key = self::$path.'cache'.md5($key);

    if (!file_exists($key) || (filemtime($key) < (time() - $seconds))) {
      return null;
    }

    if (filesize($key) == 0) {
      return null;
    }

    return(unserialize(file_get_contents($key)));
	}

	public function __set($key, $data) {
    $folder = self::$path.'cache';
    $file = $folder.'/temp-'.md5(uniqid(md5(rand()), true));
    file_put_contents($file,serialize($data));
    rename($file,$folder.'/'.md5($key));
	}

}

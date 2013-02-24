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

class baseController {

	public static $app;
	public static $config;
	public static $view;
	public static $cache;

	public function __construct(&$app=null, &$config=null, &$view=null, &$cache=null) {
		if ($app) {
			self::$app = $app;
			self::$config = $config;
			self::$view = $view;
			self::$cache = $cache;
		}
	}

	public function describe() {
		/* filter depending on security */
		/* you can over ride this in the child class if needed to provide more detail */
		return $this->details;
	}
	
	public function checkAccess($anddie=true) {
		/* security check */
		$fail = false;
		
		if ($fail) {
			self::$view->notfound();
			die();
		}
		
		return !$fail;
	}

	public function advancedGet($search,$name,$default=null,$clean=false) {
    $output = $default;

    if (isset($search[$name])) {
      $output = $search[$name];
    }

    return ($clean) ? $this->clean($output) : $output;
  }

  public function request($name,$default=null,$clean=false) {
    return $this->advancedGet($_REQUEST,$name,$default,$clean);
  }

  public function seg($num,$default=null,$clean=false) {
    return $this->advancedGet(self::$app->segs,$num,$default,$clean);
  }
  
  public function segClean($num,$default=null) {
    return $this->advancedGet(self::$app->segs,$num,$default,true);
  }

  public function segCount() {
    return count(self::$app->segs);
  }

  public function clean($input) {
    return preg_replace("/[^a-zA-Z\s0-9,\_\-\/\.]*/",'',$input);
  }

} /* end controller */

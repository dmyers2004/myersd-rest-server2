<?php
/**
* DMyers Super Simple MVC
*
* @package    Router for SSMVC
* @language   PHP
* @author     Don Myers
* @copyright  Copyright (c) 2011
* @license    Released under the MIT License.
*/

class router {

	public static $uri;
	public static $routes;
	public static $requests;

	public function __construct($config=null) {
		if ($config) {
			self::$routes = $config->get('router','routes',array());
			self::$requests = $config->get('router','requests',array());
		}
	}

	public function uri($uri) {

		foreach (self::$routes as $regex_path => $switchto) {
			$matches = array();
			if (preg_match($regex_path, $uri, $matches)) {
				self::$uri = $uri = preg_replace($regex_path, $switchto, $uri);
				return $uri;
			}
		}

		return $uri;
	}

	public function request($request) {

		foreach (self::$requests as $regex_path => $switchto) {
			$matches = array();
			if (preg_match($regex_path, strtolower($request).'/'.self::$uri, $matches)) {
				return $switchto;
			}
		}

		return $request;
	}

}

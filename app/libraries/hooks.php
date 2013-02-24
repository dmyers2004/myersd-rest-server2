<?php
/**
* DMyers Super Simple MVC
*
* @package    Hooks for SSMVC
* @language   PHP
* @author     Don Myers
* @copyright  Copyright (c) 2011
* @license    Released under the MIT License.
*
* Hooks:
* startup
* pre_router
* pre_controller
* shutdown
*/

class hooks {
	public static $app;

	public function __construct(&$app=null) {
		if ($app) {
			self::$app = $app;
		}
	}

	public function startup() {
		/* default errors */
		//error_reporting(E_ALL & ~E_NOTICE);

		/* Default timezone of server */
		date_default_timezone_set('UTC');

		new ErrorHandler;
		new Config(self::$app->path.'config/');
		new Cache(self::$app->path.'var/cache/', new Config);
		new Logger(self::$app->path.'var/logs/', new Config);
		new Output(new Config);
		new BaseController(self::$app, new Config, new Output, new Cache);
		new BaseDatabaseController();

		/* Start Session */
		/*
		session_save_path(self::$app->path.'var/sessions');
		session_name(md5(self::$app->base_url));

		session_start();
		*/
	}

	public function preRouter() {
		self::$app->request = array_merge_recursive($_GET,$_POST);
	
		$router = new Router(new Config);

		self::$app->uri = $router->uri(self::$app->raw_uri);
		self::$app->request = $router->request(self::$app->raw_request);
	}

	/* pre controller junk here */
	public function preController() {
	}

	public function shutdown() {
	}

} /* end hooks */

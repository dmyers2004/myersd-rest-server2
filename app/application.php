<?php
/**
* DMyers Super Simple MVC
*
* @package    Application File
* @language   PHP
* @author     Don Myers
* @copyright  Copyright (c) 2011
* @license    Released under the MIT License.
*/

/* Application only one of these */
class Application {

	public $default_controller = 'main';
	public $default_method = 'index';

	public function __construct($runcode = 'production') {
		/* if they send in something blank then set it to production */
		$this->run_code = $runcode;

		/* Where is this bootstrap file */
		$this->path = __DIR__.'/';

		/* Defaults to no errors displayed */
		/* if MODE = DEBUG show errors - in htaccess SetEnv MODE debug */
		$display_errors = ($runcode == 'production') ? 'Off' : 'On';
		ini_set('display_errors',$display_errors);

		/* register the autoloader */
		spl_autoload_register(array($this,'autoLoader'));

		/* is this a ajax request? */
		$this->is_ajax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

		/* try to call hook if it's there */
		$this->trigger('startup');

		/* with http:// and with trailing slash - auto detect https adjustment will be needed here */
		$this->base_url = trim('http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME']),'/');

		/* The GET method is default so controller methods look like openAction, others are handled directly openPostAction, openPutAction, openDeleteAction, etc... */
		$this->raw_request = ucfirst(strtolower($_SERVER['REQUEST_METHOD']));
		$this->request = ($this->raw_request == 'Get') ? '' : $this->raw_request;

		/* Put ANY (POST, PUT, DELETE) posted into into $_POST */
		parse_str(file_get_contents('php://input'), $_POST);

		/* get the uri (uniform resource identifier) */
		$this->uri = $this->raw_uri = trim(urldecode(substr(parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH),strlen(dirname($_SERVER['SCRIPT_NAME'])))),'/');

		/* try to call hook if it's there */
		$this->trigger('preRouter');

		/* get the uri pieces */
		$segs = explode('/',$this->uri);

		/* If they didn't include a controller and method use the defaults  main & index */
		$this->controller = (!empty($segs[0])) ? strtolower(array_shift($segs)) : $this->default_controller;
		$this->method = (!empty($segs[0])) ? strtolower(array_shift($segs)) : $this->default_method;

		/* store what ever is left over in segs */
		$this->segs = $segs;

		/* try to auto load the controller - will throw an error you must catch if it's not there */
		$classname = $this->controller.'Controller';

		/* This throws a error and 4004 - handle it in your error handler */
		if (!class_exists($classname)) {
			throw new Exception($classname.' not found',4004);
		}

		$this->main_controller = new $classname;

		/* if we are just using this single file without all the rest we need some way to reference app */
		$this->main_controller->App = $this;

		/* try to call hook if it's there */
		$this->trigger('preController');

		/* call the method - will throw an error you must catch if it's not there */
		$method = $this->method.$this->request.'Action';

		/* This throws a error and 4005 - handle it in your error handler */
		if (!is_callable(array($this->main_controller,$method))) {
			throw new Exception($classname.' method '.$method.' not found',4005);
		}

		call_user_func_array(array($this->main_controller,$method),$this->segs);

		/* try to call hook if it's there */
		$this->trigger('shutdown');
	}

	public function trigger($trigger) {
		if (class_exists('Hooks')) {
			$hook = new Hooks($this);
			$hook->$trigger();
		}
	}

	/* class autoloader */
	public function autoLoader($name) {
		if (substr($name,-10) == 'Controller') {
			$path = 'controllers';
			$name = substr($name,0,-10);
		} else {
			$path = ($name{0} >= 'A' && $name{0} <='Z') ? 'libraries' : 'models';
		}

		$load = $this->path.$path.'/'.strtolower($name).'.php';

		if (file_exists($load)) {
			require_once($load);
			return true;
		} else {
			return false;
		}
	}

} /* end mvc controller class */

<?php
/**
* DMyers Super Simple MVC
*
* @package    view for SSMVC
* @language   PHP
* @author     Don Myers
* @copyright  Copyright (c) 2011
* @license    Released under the MIT License.
*
* @Singleton
* @Inject path string
* @Inject config array()
*
*/

class output {
	public static $data = array();
	public static $output;

	public function __construct($config = null) {
		if ($config) {
			self::$output = $config->get(get_class($this),'output','json');
		}
	}

	public function set($name,$value) {
		self::$data[$name] = $value;

		return $this;
	}

	public function encode() {
		switch (self::$output) {
			case 'bson':
		    header('Content-type: application/bson');
				return bson_encode(self::$data);			
			break;
			case 'xml':
		    header('Content-type: text/xml; charset=utf-8');
				return $this->array_to_xml(self::$data, new SimpleXMLElement('<root/>'))->asXML();
			break;
			default:
		    header('Content-type: application/json');
				return json_encode(self::$data);
		}		
	}

	public function ok() {
  	header('HTTP/1.0 200 OK');		  
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    echo $this->encode();
  }

  /* Send a HTTP 201 response header. */
  public function created($url = null) {
    header('HTTP/1.0 201 Created');
    if ($url) header('Location: '.$url);
    echo $this->encode();
  }

  /* Send a HTTP 204 response header. */
  public function nocontent() {
    header('HTTP/1.0 204 No Content');
  }

  /* Send a HTTP 400 response header. */
  public function badrequest($errno=0,$errtxt='error') {
    header('HTTP/1.0 400 Bad Request');
    self::$data['errno'] = $errno;
    self::$data['errtxt'] = $errtxt;

    echo $this->encode();
  }

  /* Send a HTTP 401 response header. */
  public function unauthorized($realm = 'Private Realm') {
    header('WWW-Authenticate: Basic realm="'.$realm.'"');
    header('HTTP/1.0 401 Unauthorized');
  }

  /* Send a HTTP 404 response header. */
  public function notfound() {
    header('HTTP/1.0 404 Not Found');
  }

  /* Send a HTTP 405 response header. */
  public function methodnotallowed($allowed = 'GET, POST, PUT, DELETE') {
    header('HTTP/1.0 405 Method Not Allowed');
    header('Allow: '.$allowed);
  }

  /* Send a HTTP 406 response header. */
  public function notacceptable($input='') {
    header('HTTP/1.0 406 Not Acceptable');
    self::$data['error'] = $input;
  }

  /* Send a HTTP 500 response header. */
  public function internalservererror() {
    header('HTTP/1.0 500 Internal Server Error');
  }
  
  /* http://stackoverflow.com/a/3289602 */
  private function array_to_xml($arr, $xml) {
    foreach ($arr as $k => $v) {
      is_array($v) ? $this->array_to_xml($v, $xml->addChild($k)) : $xml->addChild($k, $v);
    }
    return $xml;
	}

}

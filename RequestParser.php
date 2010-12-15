<?php

namespace Gargantuan;

require_once('Gargantuan/Help.php');

class Request {
	public $action = 'index';
	public $id = NULL;
	public $controller;
	public $parameters = array();

	public function __construct($params = array()) {
		//$this->parameters = array();
		if(isset($_SESSION['_PARAMETERS_'])) {
			$this->setParameters($_SESSION['_PARAMETERS_']);
			unset($_SESSION['_PARAMETERS_']);
		}
		$this->setParameters($params);
	}
	public function setParameters($params) {
		foreach($params as $name => $value) {
			$this->setParameter($name,$value);
		}
	}
	public function setParameter($name,$value) {
		$this->parameters[$name] = $value;
	}
}
class RequestParser {
	protected static $re = '_/?([^/]+)_';
	protected $routes = array();
	protected $default_controller;
	protected $base_url;

	public function __construct($default_controller,$base_url = '') {
		$this->default_controller = $default_controller;
		$this->base_url = $base_url;
	}

	public function parse($request = '',$result = NULL) {
		$request = str_replace($this->base_url,'',$request);
		if(isset($_SERVER['QUERY_STRING'])) {
			$request = str_replace('?'.$_SERVER['QUERY_STRING'],'',$request);
		}
		if(NULL == $result) {
			$result = new Request($_REQUEST);
		}
		if(preg_match_all(static::$re,$request,$matches)) {
			$fields = $matches[1];
			switch(count($fields)) {
			case 3:
				$result->action = $fields[2];
			case 2:
				if(( $i = intval($fields[1]) ) > 0) {
					if($result->action == 'index') {
						$result->action = 'show';
					}
					$result->id = $fields[1];
				}
				else {
					$result->action = $fields[1];
				}
			case 1:
				$result->controller = Help::camelcase($fields[0])."Controller";
				break;
			case 0:
				$result->controller = $this->default_controller;
				break;
			}
		}
		else{
			$result->controller = $this->default_controller;
		}
		return $result;
	}
}

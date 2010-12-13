<?php

namespace Gargantuan;

require_once('Gargantuan/Help.php');

class Request {
	public $action = 'index';
	public $id = NULL;
	public $controller;
}
class Route {
	protected static $re = '_/?([^/]+)_';
	protected $routes = array();
	protected $default_controller;
	protected $base_url;

	public function __construct($default_controller,$base_url = '') {
		$this->default_controller = $default_controller;
		$this->base_url = $base_url;
	}

	public function parse($request = '') {
		$request = str_replace($this->base_url,'',$request);
		$result = new Request();
		if(preg_match_all(static::$re,$request,$matches)) {
			$fields = $matches[1];
			switch(count($fields)) {
			case 3:
				$result->action = $fields[2];
			case 2:
				if($result->action == 'index') {
					$result->action = 'view';
				}
				$result->id = $fields[1];
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

<?php

namespace Gargantuan;

//require_once('Gargantuan/Help.php');

abstract class Controller {
	protected $rendered = false;
	protected $request;

	public static function shortName() {
		return strtolower(str_replace('Controller','',get_called_class()));
	}

	public function render($action_name) {
		if(!$this->rendered) {
			$this->rendered = true;
			require(sprintf('../app/views/%s/%s.php',$this->shortName(),$action_name));
		}
	}

	public function __construct($request) {
		$this->request = $request;
	}

	public function __call($name,$parameters) {
		$this->error404($name,$parameters);
	}

	public function error404($name,$parameters) {
		header('HTTP/1.0 404 Not found');
		print("<h1>404 Page not found</h1>");
		exit();
	}


	public function get() {
		$action_name = $this->request->action;
		$this->$action_name();
	}
}

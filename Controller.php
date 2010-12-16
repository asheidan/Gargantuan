<?php

namespace Gargantuan;

//require_once('Gargantuan/Help.php');

abstract class Controller {
	protected $rendered = false;
	protected $request;

	public static function shortName() {
		return strtolower(str_replace('Controller','',get_called_class()));
	}

	public function render($template = NULL) {
		if(!$this->rendered) {
			$this->rendered = true;

			if(NULL != $template) $this->template = $template;

			if(isset($this->layout)) $this->renderLayout();
			else $this->renderTemplate();
		}
	}
	public function renderLayout() {
		require(sprintf('%sapp/views/layouts/%s.php',APP_ROOT,$this->layout));
	}
	public function renderTemplate() {
		require(sprintf('%sapp/views/%s.php',APP_ROOT,$this->template));
	}
	public function renderPartial($partial,$variables = array()) {
		foreach($variables as $name => $value) {
			$$name = $value;
		}
		require(sprintf('%sapp/views/%s.php',APP_ROOT,$partial));
	}

	public function templateExists($template) {
		return file_exists(APP_ROOT.sprintf('app/views/%s.php',$template));
	}
	public function layoutExists($layout) {
		return file_exists(APP_ROOT.sprintf('app/views/layouts/%s.php',$layout));
	}

	public function __construct($request) {
		$this->request = $request;
		$this->parameters = $request->parameters;
	}

	public function __call($name,$parameters) {
		$this->error404($name,$parameters);
	}

	public function redirectTo($path) {
		Entry::handleRedirect($path);
		$this->rendered = true;
	}

	public function error404($name,$parameters) {
		header('HTTP/1.0 404 Not found');
		print("<h1>404 Page not found</h1>");
		print("<p>$name - No page with that name was found</p>");
		exit();
	}


	public function get() {
		error_log(var_export($this->parameters,true));
		//error_log($_SERVER['REQUEST_URI']);
		//error_log(var_export($this->request,true));
		$action_name = $this->request->action;
		$this->parameters = $this->request->parameters;
		if($this->layoutExists($this->shortName())) {
			$this->layout = $this->shortName();
		}
		elseif($this->layoutExists('application')) {
			$this->layout = 'application';
		}

		$this->template = sprintf('%s/%s',$this->shortName(),$action_name);

		$this->$action_name();
		if(!$this->rendered){
			$this->render();
		}
	}
}

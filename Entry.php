<?php

namespace Gargantuan;

require_once('Gargantuan/GlobalHelpers.php');

class Entry {
	public static function autoload($class_name) {
		//error_log("Loading Gargantuan class: $class_name");
		if(preg_match('/\\\\?Gargantuan\\\\(.*)/',$class_name,$matches)) {
			//error_log(var_export($matches,true));
			include 'Gargantuan/'.$matches[1].'.php';
		}
		elseif(preg_match('/(.*)Controller/',$class_name,$matches)) {
			include 'app/controllers/'.$matches[1].'Controller.php';
		}
		elseif(preg_match('/(.*)Model/',$class_name,$matches)) {
			include 'app/models/'.$matches[1].'.php';
		}
		else {
			include $class_name.'.php';
		}
	}

	public static function handleRequest() {
		$config = ResourceManager::getConfig();
		$app_conf = $config['application'];
		$route = new Route($app_conf['default_controller'],$app_conf['baseurl']);
		$request = $route->parse($_SERVER['REQUEST_URI']);
		//print_r($_SERVER);
		//print_r($request);

		$controller_class = $request->controller;
		$controller = new $controller_class($request);

		$action_name = $request->action;

		$controller->get();
		$controller->render($action_name);
		
	}

}

spl_autoload_register(__NAMESPACE__.'\Entry::autoload');

Entry::handleRequest();

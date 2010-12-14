<?php

function link_to($text,$target,$options = array()) {
	if($target[0] == '/') {
		$config = \Gargantuan\ResourceManager::getConfig();
		$base = $config['application']['baseurl'];
		return sprintf('<a href="%s%s" %s>%s</a>',$base,$target,options_to_html($options),$text);
	}
	else {
		return sprintf('<a href="%s" %s>%s</a>',$target,options_to_html($options),$text);
	}
}

function style_link_tag($name) {
	$config = \Gargantuan\ResourceManager::getConfig();
	$base = $config['application']['baseurl'];
	return sprintf('<link rel="stylesheet" type="text/css" media="all" href="%s/stylesheets/%s.css" />',$base,$name);
}

function js_include_tag($name) {
	$config = \Gargantuan\ResourceManager::getConfig();
	$base = $config['application']['baseurl'];
	return sprintf('<script type="text/javascript" src="%s/javascripts/%s.js"></script>',$base,$name);
}
function url($path) {
	$config = \Gargantuan\ResourceManager::getConfig();
	$base = $config['application']['baseurl'];
	return sprintf('%s%s',$base,$path);
}

function html_attribute($name,$value) {
	return sprintf('%s="%s"',$name,$value);
}
function options_to_html($options) {
	return join(' ',array_map('html_attribute',array_keys($options),array_values($options)));
}
function form_tag($target, $method = 'POST',$options = array()) {
	$config = \Gargantuan\ResourceManager::getConfig();
	$base = $config['application']['baseurl'];
	return sprintf('<form action="%s%s" method="%s" %s>',
		$base,$target,$method,options_to_html($options));
}

function flash($message) {
	$_SESSION['flash'] = $message;
}
function readFlash() {
	$message = $_SESSION['flash'];
	unset($_SESSION['flash']);
	return $message;
}
function hasFlash() {
	return isset($_SESSION['flash']);
}

<?php

function link_to($text,$target,$options = array()) {
	if($target[0] == '/') {
		$config = \Gargantuan\ResourceManager::getConfig();
		$base = $config['application']['baseurl'];
		return sprintf('<a href="%s%s">%s</a>',$base,$target,$text);
	}
	else {
		return sprintf('<a href="%s">%s</a>',$target,$text);
	}
}

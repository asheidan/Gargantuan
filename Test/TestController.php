<?php

namespace Gargantuan\Test;

require_once('simpletest/autorun.php');
require_once('simpletest/mock_objects.php');
require_once('Gargantuan/Controller.php');

class_alias('Gargantuan\Controller','Controller');

\Mock::generate('Controller');

class TestController extends \UnitTestCase {
	function setUp() {
		$this->controller = new \MockController();
	}
}

<?php

namespace Gargantuan\Test;

require_once('simpletest/autorun.php');
require_once('simpletest/mock_objects.php');

require_once('Gargantuan/Controller.php');


//class MockController extends \Gargantuan\Controller {}
class MockController extends \Gargantuan\Controller {
	function index() {
		print("Nisse är en apa!!!\n");
	}
}
//class_alias('\Gargantuan\Test\MockController','MockController');
//\Mock::generate('MockController','MockMockController'); //,array('index'));

class TestController extends \UnitTestCase {
	function setUp() {
		$this->controller = new MockController();
	}

	function testEmptyGetShouldGetIndex() {
		//$this->controller->returns('index',NULL);
		//$this->controller->expectOnce('index',array());
		$this->controller->get();
	}
}

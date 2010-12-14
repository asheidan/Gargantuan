<?php


namespace Gargantuan\Test;

require_once('simpletest/autorun.php');
//require_once('simpletest/mock_objects.php');

require_once('Gargantuan/Route.php');

class MockRoute extends \Gargantuan\Route {
	public function __construct() {
		$this->default_controller = "MockController";
	}
}

class TestRoute extends \UnitTestCase {
	function setUp() {
		$this->route = new MockRoute();
	}

	function testEmptyRequest () {
		$result = $this->route->parse('/');
		$this->assertEqual('MockController',$result->controller);
		$this->assertEqual('index',$result->action);
	}

	function testSimpleRequest () {
		$result = $this->route->parse('/mock_objects/');
		$this->assertEqual("MockObjectsController",$result->controller);
		$this->assertEqual('index',$result->action);
	}

	function testSimplePhonyRequest () {
		$result = $this->route->parse('/phonys/');
		$this->assertEqual('PhonysController',$result->controller);
		$this->assertEqual('index',$result->action);
	}

	function testFullPhonyRequest () {
		$result = $this->route->parse('/phonys/42/edit');
		$this->assertEqual('PhonysController',$result->controller);
		$this->assertEqual('42',$result->id);
		$this->assertEqual('edit',$result->action);
	}

	function testNoId () {
		$result = $this->route->parse('/session/create');
		$this->assertEqual('SessionController',$result->controller);
		$this->assertEqual('create',$result->action);
		$this->assertNull($result->id);
	}
}

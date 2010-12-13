<?php

namespace Gargantuan\Test;

require_once('simpletest/autorun.php');
require_once('Gargantuan/Test/TestHelp.php');

require_once('Gargantuan/Test/TestDB.php');

require_once('Gargantuan/Test/TestRoute.php');

require_once('Gargantuan/Test/TestModel.php');

require_once('Gargantuan/Test/TestController.php');

//set_include_path(get_include_path() . PATH_SEPARATOR . 

class AllTests extends \TestSuite {
	function AllTests() {
		$this->TestSuite('All Tests');

		$this->addFile('Gargantuan/Test/TestHelp.php');
		$this->addFile('Gargantuan/Test/TestDB.php');

		$this->addFile('Gargantuan/Test/TestRoute.php');

		$this->addFile('Gargantuan/Test/TestModel.php');

		$this->addFile('Gargantuan/Test/TestController.php');
	}
}

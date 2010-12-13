<?php

namespace Gargantuan\Test;

require_once('simpletest/autorun.php');

require_once('Gargantuan/Help.php');

class TestHelp extends \UnitTestCase {
	function testUnderscore() {
		$initial = "CamelCasedString";
		$expected = "camel_cased_string";
		$this->assertEqual($expected,\Gargantuan\Help::underscore($initial));
	}

	function testCamelCase() {
		$initial = "under_scored_string";
		$expected = "UnderScoredString";
		$this->assertEqual($expected,\Gargantuan\Help::camelcase($initial));
	}
}

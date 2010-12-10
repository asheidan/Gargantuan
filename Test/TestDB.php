<?php

namespace Gargantuan\Test;

require_once('simpletest/autorun.php');
require_once('simpletest/mock_objects.php');

require_once('Gargantuan/DB.php');

class_alias('Gargantuan\DB','DB');

\Mock::generate('DB');
\Mock::generate('PDO');

class TestDB extends \UnitTestCase {
	function testQueryCallsCorrectMethod() {
		$sql = 'SELECT * FROM some_table WHERE some_table.column_name = 42';
		$pdo = new \MockPDO();
		$pdo->expectOnce('query',array($sql));
		$db = new \DB($pdo);
		$db->query($sql);
	}

	function testQouteCallsCorrectMethod() {
		$st = "Long winded String";
		$pdo = new \MockPDO();
		$pdo->expectOnce('quote',array($st));
		$db = new \DB($pdo);
		$db->quote($st);
	}

	function testExecCallsCorrectMethod() {
		$sql = "INSERT INTO table_name (id,name) VALUES (42,'Pelle Nissesson')";
		$pdo = new \MockPDO();
		$pdo->expectOnce('exec',array($sql));
		$db = new \DB($pdo);
		$db->exec($sql);
	}

}

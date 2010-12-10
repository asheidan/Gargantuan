<?php

namespace Gargantuan;

class ResourceManager {
	private static $pdo;
	private static function getPDO($dsn = NULL,$user = NULL, $pass = NULL) {
		if(!isset(static::$pdo)) {
			if(NULL == $dsn) {
				static::$pdo = new PDO('sqlite:test.db',NULL,NULL);
			}
			else {
				static::$pdo = new PDO($dsn,$user,$pass);
			}
		}
	}

	private static $db;
	public static function getDB() {
		if(!isset(static::$db)) {
			static::$db = new DB(static::getPDO());
		}
		return static::$db;
	}

	public static function setDB($db) {
		static::$db = $db;
	}

	public static function resetDB($dsn = NULL,$user = NULL,$pass = NULL) {
		static::$db = new DB(static::getPDO($dsn));
	}
}

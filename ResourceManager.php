<?php

namespace Gargantuan;

class ResourceManager {
	private static $config;

	public static function getConfig() {
		if(!isset(static::$config)) {
			static::$config = parse_ini_file('../config/app.ini',true);
			//print("<pre>");
			//print_r(static::$config);
		}
		return static::$config;
	}

	private static $pdo;
	private static function getPDO($dsn = NULL,$user = NULL, $pass = NULL) {
		if(!isset(static::$pdo)) {
			if(NULL == $dsn) {
				$config = static::getConfig();
				$db = $config['database'];
				static::$pdo = new \PDO(sprintf("%s:dbname=%s",$db['dbdriver'],$db['dbname']),$db['user'],$db['pass']);
			}
			else {
				static::$pdo = new \PDO($dsn,$user,$pass);
			}
		}
		return static::$pdo;
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

<?php

namespace Gargantuan;

class DB {
	private $pdo;
	private $debug;
	public function query($sql) {
		if($this->debug) error_log($sql);
		return $this->pdo->query($sql);
	}
	public function quote($value) {
		return $this->pdo->quote($value);
	}
	public function exec($sql) {
		if($this->debug) error_log($sql);
		return $this->pdo->exec($sql);
	}

	public function __construct(&$pdo, $debug = false) {
		$this->pdo = $pdo;
		$this->debug = $debug;
	}

	public function setDebug($debug) {
		$this->debug = $debug;
	}

}

<?php

namespace Gargantuan;

class DB {
	private $pdo;
	public function query($sql) {
		return $this->pdo->query($sql);
	}
	public function quote($value) {
		return $this->pdo->quote($value);
	}
	public function exec($sql) {
		return $this->pdo->exec($sql);
	}

	public function __construct(&$pdo) {
		$this->pdo = $pdo;
	}

}

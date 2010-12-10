<?php
/**
 * Gargantuan/Model.php
 */

namespace Gargantuan;

require_once('Gargantuan/Help.php');

class Model {
	/**
	 * Static/Class
	 */
	protected static $schema;
	protected static $db;

	protected static function getSchema() {
		if(!isset(static::$schema)) {
			$db = ResourceManager::getDB();
			$sql = sprintf(
				'SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = %s ORDER BY ORDINAL_POSITION ASC',
				$db->quote(static::tableName())
			);
			$result = $db->query($sql);
			static::$schema = $result->fetchColumn();
		}
		return static::$schema;
	}


	public static function tableName() {
		return 
			preg_replace('/^(.*\\\\)?/','',Help::underscore(get_called_class())).'s';
	}

	public static function find($id) {
		$sql = sprintf(
			"SELECT %s.* FROM %s WHERE id = %d",
			static::tableName(),static::tableName(),$id
		);
		$result = ResourceManager::getDB()->query($sql);
		return $result->fetchObject(get_called_class(),array(array(),false));
	}


	/**
	 * Instance
	 */
	protected $dirty = array();
	protected $data = array();
	protected $new;
	protected $id;

	public function __construct($initial = array(),$new = true) {
		static::getSchema();
		$this->new = $new;
		$this->dirty = array();
		foreach($initial as $key => $value) {
			$this->__set($key,$value);
		}
	}

	public function __set($name,$value) {
		if($name == 'id') {
			$this->id = $value;
		}
		elseif(in_array($name,static::getSchema())) {
			$this->data[$name] = $value;
			$this->dirty[$name] = $value;
		}
	}

	public function __get($name) {
		if(isset($this->data[$name])) {
			return $this->data[$name];
		}
		else {
			throw new \Exception("Undefined Property");
		}
	}

	public function isDirty() {
		return count($this->dirty) > 0;
	}
	public function isNew() {
		return $this->new;
	}

	public function save() {
		if($this->isDirty()) {
			$db = ResourceManager::getDB();
			$sql = NULL;
			if($this->isNew()) {
				$sql = sprintf(
					'INSERT INTO %s (%s) VALUES (%s)',
					static::tableName(),join(',',array_keys($this->dirty)),
					join(',',array_map(
						function($value) {
							return ResourceManager::getDB()->quote($value);
						},array_values($this->dirty)
					))
				);
			}
			else {
				$sql = sprintf(
					'UPDATE %s SET %s WHERE id = %d',
					static::tableName(),
					join(',',array_map(
						'static::sqlFragSetField',
						array_keys($this->dirty),
						array_values($this->dirty)
					)),$this->id
				);
			}
			return $db->exec($sql);
		}
	}

	protected static function sqlFragSetField($name,$value){
		return sprintf('%s = %s',$name,ResourceManager::getDB()->quote($value));
	}
}

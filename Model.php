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
	protected static $validations = array();
	/**
	 * Relations
	 * array(
	 *		type => hasMany
	 *		class => class_name
	 *		localKey =>
	 *		remoteKey =>
	 */
	protected static $relations = array();

	protected static function getSchema() {
		if(!isset(static::$schema)) {
			$db = ResourceManager::getDB();
			$config = ResourceManager::getConfig();
			$sql = sprintf(
				'SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_schema = %s AND table_name = %s ORDER BY ORDINAL_POSITION ASC',
				$db->quote($config['database']['dbname']),$db->quote(static::tableName())
			);
			$result = $db->query($sql);
			static::$schema = $result->fetchAll(\PDO::FETCH_COLUMN,0);
			if(NULL == static::$schema) {
				static::$schema = array();
			}
		}
		return static::$schema;
	}


	public static function tableName() {
		if(isset(static::$table)) {
			return static::$table;
		}
		else {
			return 
				preg_replace('/^(.*\\\\)?/','',Help::underscore(get_called_class())).'s';
		}
	}

	public static function find($id = 'all') {
		if($id == 'all') {
			$sql = sprintf(
				'SELECT %s.* FROM %s',
				static::tableName(), static::tableName()
			);
			$result = ResourceManager::getDB()->query($sql);
			return $result->fetchAll(\PDO::FETCH_CLASS,get_called_class(),array(array(),false));
		}
		elseif(true){
			$sql = sprintf(
				"SELECT %s.* FROM %s WHERE id = %d",
				static::tableName(),static::tableName(),$id
			);
			$result = ResourceManager::getDB()->query($sql);
			//error_log("in find: " . get_called_class());
			//var_dump($result);
			return $result->fetchObject(get_called_class(),array(array(),false));
		}
	}

	public static function findBySQL($sql) {
		$result = ResourceManager::getDB()->query($sql);
		return $result->fetchAll(\PDO::FETCH_CLASS, get_called_class(),array(array(),false));
	}
	public static function findByFields($fields) {
		if(count($fields) > 0) {
			$field_data = join(' AND ',array_map('static::sqlFragSetField',array_keys($fields),array_values($fields)));
			$sql = sprintf(
				'SELECT %s.* FROM %s WHERE %s',
				static::tableName(),static::tableName(),$field_data
			);
			$result = ResourceManager::getDB()->query($sql);
			return $result->fetchAll(\PDO::FETCH_CLASS,get_called_class(),array(array(),false));
		}
		else {
			return array();
		}
	}



	/**
	 * Instance
	 */
	protected $dirty = array();
	protected $data = array();
	protected $new;
	protected $id;

	public function __construct($initial = array(),$new = true) {
		//error_log(get_class($this));
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
		else {
			if(in_array($name,static::getSchema())) {
				$this->dirty[$name] = $value;
			}
			$this->data[$name] = $value;
		}
	}

	public function __get($name) {
		if(isset($this->data[$name])) {
			return $this->data[$name];
		}
		elseif($name == 'id') {
			return $this->id;
		}
		elseif(isset(static::$relations[$name])) {
			$r = static::$relations[$name];
			if(isset($r['localKey'])) {
				$local_key = $r['localKey'];
			}
			else {
				$local_key = 'id';
			}
			if(isset($r['remoteKey'])) {
				$remote_key = $r['remoteKey'];
			}
			else {
				$remote_key = 'id';
			}
			switch($r['type']) {
			case "hasMany":
				$this->data[$name] = $r['class']::findByFields(array($remote_key => $this->$local_key));
				break;
			case "belongsTo":
				$a = $r['class']::findByFields(array($remote_key => $this->$local_key));
				$this->data[$name] = $a[0];
				break;				
			}
			return $this->data[$name];
		}
		else {
			return NULL;
		}
	}

	public function isDirty() {
		return count($this->dirty) > 0;
	}
	public function isNew() {
		return $this->new;
	}

	public function validate() {
		$class_name = get_called_class();
		foreach(static::$validations as $name => $validation) {
			if(!$class_name::$validation($this->data[$name])) {
				flash(sprintf('%s is not valid',$name),'error');
				return false;
			}
		}
		return true;
	}

	public function save() {
		if($this->isDirty() and $this->validate()) {
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
		return false;
	}

	protected static function sqlFragSetField($name,$value){
		return sprintf('%s = %s',$name,ResourceManager::getDB()->quote($value));
	}
}

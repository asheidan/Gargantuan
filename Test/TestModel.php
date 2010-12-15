<?php

namespace Gargantuan\Test;

require_once('simpletest/autorun.php');
require_once('simpletest/mock_objects.php');
require_once('Gargantuan/DB.php');
require_once('Gargantuan/Model.php');
require_once('Gargantuan/ResourceManager.php');

//use \Gargantuan as Gargantuan;

//class_alias('Gargantuan\Model','Model');
class_alias('Gargantuan\DB','DB');

\Mock::generate('PDO');
\Mock::generate('DB');
\Mock::generate('PDOStatement');

class MockModel extends \Gargantuan\Model {
	protected static $relations = array(
		'children' => array(
			'type' => 'hasMany',
			'class' => 'Gargantuan\Test\MockChild',
			'remoteKey' => 'parent_id',
		),
		'parent' => array(
			'type' => 'belongsTo',
			'class' => 'Gargantuan\Test\MockModel',
			'localKey' => 'name'
		)
	);
}
class MockChild extends \Gargantuan\Model {}

class TestModel extends \UnitTestCase {
	function setUp() {
		//print_r(get_declared_classes());
		$schema_query = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = 'mock_models' ORDER BY ORDINAL_POSITION ASC";
		$this->stmt = new \MockPDOStatement();
		$this->stmt->returns('fetchAll',array('id','name'));

		$this->db = new \MockDB();
		//$this->db->expectOnce('query',array($schema_query));
		$this->db->returns(
			'query',
			$this->stmt,
			$schema_query
		);
		\Gargantuan\ResourceManager::setDB($this->db);
		$this->model = &new MockModel();
	}

	function testShouldCreateObject() {
		$this->assertNotNull($this->model);
	}

	function testCleanObjectShouldntSave() {
		$db = new \MockDB();
		$db->expectNever('exec');
		$db->returns('query',new \MockPDOStatement());
		\Gargantuan\ResourceManager::setDB($db);
		$model = new MockModel();
		$model->save();
	}

	function testSettingAndGettingTheSameAttributeShouldReturnTheSame() {
		$this->model->name = "Kalle Anka";
		$this->assertEqual($this->model->name,"Kalle Anka");
	}

	function testNewShouldBeClean() {
		$this->assertFalse($this->model->isDirty());
	}

	function testDirtyShouldNotBeClean() {
		$this->model->name = "Kalle Anka";
		$this->assertTrue($this->model->isDirty());
	}

	function testShouldHaveProperTableName() {
		$this->assertEqual("models",\Gargantuan\Model::tableName());
		$this->assertEqual("mock_models",$this->model->tableName());
	}

	//function testShouldGetAttributesFromDBOnFirstCreation() {

		//$db = new \MockDB();
		//$db->returns('quote',"'mock_models'");
		//$db->expectOnce('quote',array('mock_models'));

		//\Gargantuan\ResourceManager::setDB($db);
		//$model = &new MockModel();
	//}

	function testDirtyNewModelShouldSaveValidAttributes() {
		$insert_sql = "INSERT INTO mock_models (name) VALUES ('Arne Nissesson')";
		$db = new \MockDB();
		$db->returns('quote',"'Arne Nissesson'",array("Arne Nissesson"));
		$db->returns('exec',true,array($insert_sql));
		$db->expectOnce('exec',array($insert_sql));
		$db->returns('query',new \MockPDOStatement());

		\Gargantuan\ResourceManager::setDB($db);
		
		$model = new MockModel();

		$model->name = "Arne Nissesson";
		$model->arne = "Polaren Pär";
		$this->assertTrue($model->save());
	}

	function testFetchFromDBShouldUseProperSQL() {
		$fetch_sql = "SELECT mock_models.* FROM mock_models WHERE id = 1";
		$db = new \MockDB();
		$db->returns('query',new \MockPDOStatement());
		$db->expectOnce('query',array($fetch_sql));

		\Gargantuan\ResourceManager::setDB($db);
		
		MockModel::find(1);
	}

	function testNewlyCreatedWithValuesShouldBeDirty() {
		$model = new MockModel(array('name' => 'Sara Isaksson'));
		$this->assertTrue($model->isDirty());
	}

	function testNotNewShouldUseUpdate() {
		$update_sql = "UPDATE mock_models SET name = 'Pelle Karlsson' WHERE id = 42";
		$db = new \MockDB();
		$db->expectOnce('exec',array($update_sql));
		$db->returns('exec',true);
		$db->expectOnce('quote',array("Pelle Karlsson"));
		$db->returns('quote',"'Pelle Karlsson'",array("Pelle Karlsson"));
		$db->returns('query',new \MockPDOStatement());

		\Gargantuan\ResourceManager::setDB($db);

		$model = new MockModel(array('name'=>'Pelle Karlsson','id'=>42),false);
		$this->assertTrue($model->save());
	}

	function testFindBySQL() {
		$find_sql = "SELECT users.* FROM users WHERE users.id = 42 AND name LIKE '%Arne%'";
		$db = new \MockDB();
		$db->expectOnce('query',array($find_sql));
		$db->returns('query',new \MockPDOStatement());

		\Gargantuan\ResourceManager::setDB($db);

		MockModel::findBySQL($find_sql);
	}

	function testFindByFields() {
		$find_sql = "SELECT mock_models.* FROM mock_models WHERE id = 42 AND name = 'arne'";
		$db = new \MockDB();
		$db->expectOnce('query',array($find_sql));
		$db->returns('query',new \MockPDOStatement());
		$db->returns('quote',42,array(42));
		$db->returns('quote',"'arne'",array('arne'));

		\Gargantuan\ResourceManager::setDB($db);

		MockModel::findByFields(array('id' => 42,'name'=>'arne'));
	}

	function testChildRelationsShouldBeFetchedWhenCalled() {
		$fetch_sql = 'SELECT mock_childs.* FROM mock_childs WHERE parent_id = 42';
		$db = new \MockDB();
		$db->expectOnce('query',array($fetch_sql));
		$db->returns('query',new \MockPDOStatement());
		$db->returns('quote',42,array(42));

		\Gargantuan\ResourceManager::setDB($db);

		$model = new MockModel(array('id'=>42),false);
		$children = $model->children;
	}

	function testChildRelationsShouldUseCorrectLocalKey() {
		$fetch_sql = "SELECT mock_models.* FROM mock_models WHERE id = 'arne'";
		$db = new \MockDB();
		$db->expectOnce('query',array($fetch_sql));
		$db->returns('query',new \MockPDOStatement());
		$db->returns('quote',"'arne'",array('arne'));

		\Gargantuan\ResourceManager::setDB($db);

		$model = new MockModel(array('id'=>42,'name'=>'arne'),false);
		$parent = $model->parent;
	}
}

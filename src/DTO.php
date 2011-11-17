<?php

require_once dirname(__FILE__).'/../config.php';

// Dummy class - TODO extract
class Database {

	public static function queryOne() {}
	public static function query() {}

}

class DTO {

	var $id;

	public function getId(
	) {
		return $this->id;
	}

	public function setId(
		$id = -1
	) {
		$this->id = $id;
	}

	public function __construct(
		$data = null
	) {
		$this->setId();
		if ($data != null)
			foreach($data as $key => $value) {
				$setterName = "set".ucfirst($key);
				preg_match_all('/setVi_(?P<name>.*)_id/', $setterName, $matches);
				if (count($matches['name'])) {
					// relationship with class $otherClass
					$otherClass = ucfirst($matches['name'][0]);
					$setterName = "set$otherClass";
					call_user_func(
						array($this, $setterName), 
						self::getById($value, $otherClass)
					);
				} else {
					call_user_func(
						array($this, $setterName), 
						$value
					);
				}
			}
	}

	public static function getById(
		$id,
		$className = null
	) {
		if ($id == null || $id == "")
			return null;
		if ($className == null)
			$className = get_called_class();
		$tableName = "vi_".lcfirst($className);
		$data = Database::queryOne("SELECT * FROM $tableName WHERE id=$id");
		if ($data != null) {
			$reflectionObj = new ReflectionClass($className);
			return $reflectionObj->newInstanceArgs(array($data)); 
		}
		return null;
	}

	protected function getRelatedInstances(
		$otherClass,
		$orderFields = array()
	) {
		$className = get_called_class();
		$tableName = "vi_".lcfirst($otherClass);
		$joinField = "vi_".lcfirst($className)."_id";
		if (count($orderFields) > 0) {
			$items = implode(", ", $orderFields);
			$orderClause = "ORDER BY $items";
		} else {
			$orderClause = "";
		}
		$rows = Database::query("SELECT * FROM $tableName WHERE $joinField={$this->id} $orderClause");
		$instances = array();
		foreach ( $rows as $row ) {
			$reflectionObj = new ReflectionClass($otherClass);
			$instances[]= $reflectionObj->newInstanceArgs(array($row)); 
		}
		return $instances;
		
	}

	// TODO change name, since this is not a builder but a retriever, maybe "find"?
	public static function getInstance(
		$restrictions,
		$className = null
	) {
		$instances = self::getInstances($restrictions, $className);
		return (count($instances) > 0)
			? $instances[0]
			: null;
	}

	public static function getInstances(
		$restrictions = array(),
		$className = null
	) {
		// TODO abstract join fields in restrictions
		if ($className == null)
			$className = get_called_class();
		$tableName = "vi_".lcfirst($className);

		$whereClause = "";
		$first = true;
		foreach ($restrictions as $field => $value) {
			if ($first)
				$first = false;
			else
				$whereClause .= " AND ";
			$whereClause .= "($field = '$value')";
		}
		if ($whereClause == "")
			$whereClause = "1 = 1";

		//$data = Database:query("SELECT * FROM $tableName WHERE ($whereClause)");
		$instances = array();
		if ($data != null) {
			foreach ($data as $row) {
				$reflectionObj = new ReflectionClass($className);
				$instances[] = $reflectionObj->newInstanceArgs(array($row)); 
			}
		}
		return $instances;
	}

	public function save(
	) {
		$mappings = $this->getMappings();
		if ($this->exists_on_db())
			$this->update_on_db($mappings);
		else
			$this->insert_to_db($mappings);
	}

	private function getTableName(
	) {
		return "vi_".lcfirst(get_called_class());
	}

	private function exists_on_db(
	) {
		return ($this->id != null) && $this->getById($this->id) != null;
	}

	private function update_on_db(
		$mappings
	) {
		$assignments = "";
		$first = true;
		foreach ($mappings as $field => $value) {
			if ($first)
				$first = false;
			else
				$assignments .= ", ";
			if ($this->isJoinField($field)) {
				$value = $this->cascadeOn($value);
			}
			$assignments .= "$field = '$value'";
		}
		$query = "UPDATE {$this->getTableName()} SET $assignments WHERE id={$this->id}";
		Database::query($query);
	}

	private function isJoinField(
		$field
	) {
		return (StringUtils::startsWith($field, 'vi_') 
				&& StringUtils::endsWith($field, '_id'));
	}

	private function cascadeOn(
		$object
	) {
		$object->save();
		return $object->getId();
	}

	private function insert_to_db(
		$mappings
	) {
		$goesWithId = false;
		$fields = "";
		$values = "";
		$first = true;
		foreach ($mappings as $field => $value) {
			if ($first)
				$first = false;
			else {
				$fields .= ", ";
				$values .= ", ";
			}
			if ($field == "id")
				$goesWithId = true;
			if ($this->isJoinField($field)) {
				$value = $this->cascadeOn($value);
			}
			$fields .= $field;
			$values .= "'$value'";
		}
		$query = "INSERT INTO {$this->getTableName()} ($fields) VALUES ($values)";
		Database::query($query);
		if (!$goesWithId) {
			$row = Database::queryOne("SELECT MAX(id) as id FROM {$this->getTableName()}");
			$newId = $row['id'];
			if ($newId !== null)
				$this->setId($newId);
		}
	}

	private function getMappings(
	) {
		$mappings = array();
		foreach (get_class_methods(get_called_class()) as $method) {
			if (StringUtils::startsWith($method, "get") && !in_array($method, get_class_methods("DTO"))) {
				$fieldName = lcfirst(substr($method, 3));

				if ($this->is_1_relationship($fieldName)) {
					$fieldName = "vi_{$fieldName}_id";
					$mappings[$fieldName] = call_user_func(array($this, $method));
				} else if ($this->is_n_relationship($fieldName)) {
					// TODO cascade?
				} else {
					$value = call_user_func(array($this, $method));
					if ($fieldName != "id" || $value != "-1")
						$mappings[$fieldName] = $value;
				}
			}
		}
		return $mappings;
	}

	private function is_1_relationship(
		$fieldName
	) {
		try {
			$reflectionObj = new ReflectionClass(ucfirst($fieldName));
		} catch (Exception $e) {
			return false;
		}
		return ($reflectionObj != null) && ($reflectionObj->getParentClass()->getName() == "DTO");
	}

	private function is_n_relationship(
		$fieldName
	) {
		try {
			$reflectionObj = new ReflectionClass(ucfirst(substr($fieldName, 0, strlen($fieldName) - 1)));
		} catch (Exception $e) {
			return false;
		}
		return ($reflectionObj != null) && ($reflectionObj->getParentClass()->getName() == "DTO");
	}

}

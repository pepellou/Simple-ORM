<?php

require_once dirname(__File__).'/../config.php';

	class DTOTest extends PHPUnit_Framework_TestCase {

		public function test_canCreate (
		) {
			new DTO();
		}

		public function test_newDTOIsntSaved(
		) {
			$dto = new DTO();
			$this->assertFalse($dto->isSaved());	
		}

		public function test_canSetValuesOnBuild(
		) {
			$obj = new PersistibleObject(array("name" => "test"));
			$this->assertEquals("test", $obj->getName()); 
		}

		public function test_canSave(
		) {
			$dto = new DTO();
			$dto->save();
			$this->assertTrue($dto->isSaved());	
		}

		public function test_getByIdReturnsNullWhenNoObjectsPersisted(
		) {
			$obj = new PersistibleObject(array("name" => "test"));
			$this->assertNull(PersistibleObject::getById(1));
		}

		public function test_onSaveItsPersisted(
		) {
			$obj = new PersistibleObject(array("name" => "test"));
			$obj->save();
			$this->assertNotNull(PersistibleObject::getById(1));
			$this->assertEquals($obj, PersistibleObject::getById(1));
		}

	}


	class PersistibleObject extends DTO{

		private $name;

		public function setName(
			$name
		) {
			$this->name = $name;
		}
		
		public function getName(
		) {
			return $this->name;
		}
	}

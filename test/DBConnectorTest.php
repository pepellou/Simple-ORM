<?php
	require_once dirname(__FILE__).'/../src/DBConnector.php';

class DBConnectorTest extends PHPUnit_Framework_TestCase {

	private function getValidConnector(){
		$validHost = '192.168.0.49';
		$validUser = 'facturador';
		$validPass = '76900831.d';
		$validDB = 'FacturAZ';
		return new DBConnector($validHost, $validUser, $validPass, $validDB);	
	}

	/**
	* @expectedException CannotConnectException
	*/
	public function test_cannotCreateInvalidConnector(
	) {
		$invalidHost = 'invalidHost';
		$invalidUser = '';
		$invalidPass = '';
		$invalidDB = '';
		new DBConnector($invalidHost, $invalidUser, $invalidPass, $invalidDB);	
	}

	/**
	* @expectedException NoSuchDatabaseException
	*/
	public function test_cannotCreateWithInvalidDB(
	) {
		$validHost = '192.168.0.49';
		$validUser = 'facturador';
		$validPass = '76900831.d';
		$invalidDB = '';
		new DBConnector($validHost, $validUser, $validPass, $invalidDB);	
	}

	public function test_canCreateValidConnector(
	) {
		$this->getValidConnector();
	}

	public function test_connectionIsClosedAfterCreating(
	) {
		$connector = $this->getValidConnector();
		$this->assertFalse($connector->hasOpenConnection());
	}

} 
?>

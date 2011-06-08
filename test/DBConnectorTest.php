<?php
	require_once dirname(__FILE__).'/../src/DBConnector.php';

class DBConnectorTest extends PHPUnit_Framework_TestCase {

	/**
	* @expectedException CannotConnectException
	*/
	public function test_cannotCreateInvalidConnection(
	) {
		$invalidUser = '';
		$invalidPass = '';
		new DBConnector($invalidUser,$invalidPass);	
	}
} 
?>

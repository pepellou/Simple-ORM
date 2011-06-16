<?php

class CannotConnectException extends Exception {

}

class NoSuchDatabaseException extends Exception {
}

class DBConnector {

	private $connection;
	private $username;
	private $password;

	public function __construct($host, $username, $password, $db) {
		try {
			$this->connection = mysql_connect($host, $username, $password);
			if(!mysql_select_db($db, $this->connection))
				throw new NoSuchDatabaseException();
			mysql_close($this->connection);
		} catch (NoSuchDatabaseException $e) {
			throw new NoSuchDatabaseException();
		} catch (Exception $e) {
			throw new CannotConnectException();
		}
	}

	public function hasOpenConnection(){
		return is_resource($this->connection);
	}

	public function select($language, $sql){
		$this->doLog($sql);
		$this->openConnection();
		mysql_select_db($this->getDbName($language), $this->connection);
		$result = mysql_query($sql, $this->connection);
		if (mysql_errno($this->connection)) {
			throw new Exception("Error while executing query: " . $sql);
		}
		$rows = array();
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
			$rows[] = $row;
		}
		$this->closeConnection();
		return $rows;
	}

	public function update($language, $sql){
		$this->doLog($sql);
		$this->openConnection();
		mysql_select_db($this->getDbName($language), $this->connection);
		mysql_query($sql);
		if (mysql_errno($this->connection)) {
			throw new Exception("Error while executing query: " . $sql);
		}
		$this->closeConnection();
	}

	public function selectOne($language, $sql){
		$this->doLog($sql);
		$this->openConnection();
		mysql_select_db($this->getDbName($language), $this->connection);
		$result = mysql_query($sql, $this->connection);
		if (mysql_errno($this->connection)) {
			throw new Exception("Error while executing query: " . $sql);
		}
		$rows = mysql_fetch_array($result, MYSQL_BOTH);
		$this->closeConnection();
		return $rows[0];
	}

	public function truncate($language, $tableName){
		$this->openConnection();
		mysql_select_db($this->getDbName($language), $this->connection);
		mysql_query("TRUNCATE " . $tableName, $this->connection);
		$this->closeConnection();
	}
}

?>

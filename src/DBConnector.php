<?php

class CannotConnectException extends Exception {

}

class DBConnector {

    private $connection;
    private $username;
    private $password;

    public function __construct($username, $password) {
	throw new CannotConnectException();
    }

    public function openConnection() {
        $this->connection = mysql_connect(Config::getHost(),$this->username, $this->password);
        if (!$this->connection)
            echo 'connection fail';
    }

    public function getConnection(){
        return $this->connection;
    }

    public function getDBName($language){
        return ($language == LANG_ES) ? "culturactiva_ES" : "culturactiva";
    }

    public function closeConnection(){
        mysql_close($this->connection);
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

    public function getTables(){
          return array(
            "agenda_has_espectaculo",
            "linkAgenda",
            "agenda",
            "etiqueta_has_espectaculo",
            "etiqueta",
            "dossierEspectaculo",
            "linkEspectaculo",
            "fotoEspectaculo",
            "espectaculo",
            "fotoArtista",
            "dossierArtista",
            "linkArtista",
            "artista",
            "pagina",
            "boletin"
        );
    }

    public function deleteAll(){
        foreach (Config::getRegisteredLanguages() as $language) {
            foreach ($this->getTables() as $table) {
                $this->truncate($language, $table);
            }
        }
    }

    public function doLog($sql){
        // TODO truncate each field instead the whole line
        $maxLen = 150;
        if (strlen($sql) > $maxLen)
            $sql = substr($sql, 0, $maxLen) . '...';
        Logger::info('SQL: ' . $sql);
    }
}

?>

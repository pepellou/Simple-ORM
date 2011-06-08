<?php

require_once 'configuration.php';
require_once 'DBConnector.php';

class Database {


    static $username = 'culturactiva';
    static $password = '76900831.d';
    private static $connection;
    private static $dbConnector;

    private static function getDBConnector() {
        if (self::$dbConnector == null) {
            self::$dbConnector = new DBConnector(self::$username, self::$password);
        }
        return self::$dbConnector;
    }

    public static function init($connector=null) {
        self::$dbConnector = $connector;
    }

    private static function openConnection() {

        self::getDBConnector()->openConnection();
        self::$connection = self::getDBConnector()->getConnection();
    }

    private static function getDbName($language) {
        return self::getDBConnector()->getDBName($language);
    }

    private static function closeConnection(
    ) {
        self::getDBConnector()->closeConnection();
    }

    static function update($language, $sql) {
       self::getDBConnector()->update($language, $sql);
    }

    static function select($language, $sql) {
        return self::getDBConnector()->select($language, $sql);
    }

    static function selectOne($language, $sql) {
        return self::getDBConnector()->selectOne($language, $sql);
    }

    static function truncate($language, $tableName) {
        self::getDBConnector()->truncate($language, $tableName);
    }

    /**
     * Returns all tables ordered according to proper cascade deleting order
     */
    private static function getTables() {
      self::getDBConnector()->getTables();
    }

    public static function deleteAll() {
        self::getDBConnector()->deleteAll();
    }

    private static function doLog($sql) {
        self::getDBConnector()->doLog($sql);
    }

}

;
?>

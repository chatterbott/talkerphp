<?php

/*
 * Available suggestiveSave types:
 * database-mysql
 * file_txt
 */

class Config {

    private $dbHost = "localhost";
    private $dbUser = "root";
    private $dbPass = "1234";
    private $dbName = "talkerphp";
    private $suggestiveSave = "file_txt";
    private static Config $singleton;

    private function __construct() {

    }

    public static function getInstance() {
        if (!isset(self::$singleton)) {
            self::$singleton = new Config ();
        }
        return self::$singleton;
    }

    public function getDbHost() {
        return $this->dbHost;
    }

    public function getDbUser() {
        return $this->dbUser;
    }

    public function getDbPass() {
        return $this->dbPass;
    }

    public function getDbName() {
        return $this->dbName;
    }

    public function getSuggestiveSave() {
        return $this->suggestiveSave;
    }

}

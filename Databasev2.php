<?php

class Database {
    /**
     * @var Database
     */
    protected static $_dbInstance = null;

    /**
     * @var PDO
     */
    protected $_dbHandle;

    /**
     * @return Database
     */
    public static function getInstance() {
        $username ='[Username Goes Here]';
        $password = '[Password Goes Here]';
        $host = '[Host Goes Here]';
        $dbName = '[Database Name goes Here]';

        if(self::$_dbInstance === null) { //check to see if the PDO exists
            //If it doesnt exist, make a new one using the connection info
            self::$_dbInstance = new self($username, $password, $host, $dbName);
        }

        return self::$_dbInstance;
    }

    /**
     * @param $username
     * @param $password
     * @param $host
     * @param $database
     */
    private function __construct($username, $password, $host, $database) {
        try {
            $this->_dbHandle = new PDO("mysql:host=$host;dbname=$database",  $username, $password); // creates the database handle with connection info
            //$this->_dbHandle = new PDO('mysql:host=' . $host . ';dbname=' . $database,  $username, $password); // creates the database handle with connection info

        }
        catch (PDOException $e) { //If connection to DB fails, catch the error
            echo $e->getMessage();
        }
    }

    /**
     * @return PDO
     */
    public function getdbConnection() {
        return $this->_dbHandle; //Returns the PDO handle                                      elsewhere
    }

    public function __destruct() {
        $this->_dbHandle = null; //Destroy the PDO handle when its no longer needed
    }
}

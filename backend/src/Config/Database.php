<?php
namespace Config;

class Database {
    private $manager;
    private $dbname = 'tncf';

    public function __construct() {
        // connection for MongoDB inside a Docker
        try {
            $this->manager = new \MongoDB\Driver\Manager("mongodb://db:27017");
        } catch (\MongoDB\Driver\Exception\Exception $e) {
            die("Помилка підключення до MongoDB: " . $e->getMessage());
        }
    }

    public function getManager() {
        return $this->manager;
    }

    public function getDbName() {
        return $this->dbname;
    }
}
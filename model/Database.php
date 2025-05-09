<?php
// model/Database.php

class Database {
    /** @var \PDO */
    private static $instance = null;

    /**
     * Returns the singleton PDO instance.
     *
     * @return \PDO
     */
    public static function getInstance(): \PDO {
        if (self::$instance === null) {
            $host = 'localhost';
            $db   = 'project';
            $user = 'root';
            $pass = '';
            $dsn  = "mysql:host=$host;dbname=$db;charset=utf8mb4";

            try {
                self::$instance = new \PDO($dsn, $user, $pass);
                self::$instance->setAttribute(
                    \PDO::ATTR_ERRMODE,
                    \PDO::ERRMODE_EXCEPTION
                );
            } catch (\PDOException $e) {
                die("Database connection failed: " . $e->getMessage());
            }
        }
        return self::$instance;
    }

    /** Prevent direct instantiation */
    private function __construct() {}
    private function __clone() {}
    public function __wakeup() {}
}

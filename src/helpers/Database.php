<?php

class Database
{
    protected $db;
    protected $user;
    protected $password;
    protected $host;
    private static $pdo;

    public function __construct()
    {
        $this->host = 'localhost';
        $this->db = 'webservice';
        $this->user = 'root';
        $this->password = '';

        $dsn = "mysql:dbname={$this->db};host={$this->host}";

        try {
            self::$pdo = new PDO($dsn, $this->user, $this->password);
        } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
        }
    }

    public static function getInstance()
    {
        if(!isset(self::$pdo)) {
            new Database();
        }

        return self::$pdo;
    }

}
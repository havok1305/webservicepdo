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
        $this->password = 'root';

        $dsn = "mysql:dbname={$this->db};host={$this->host};charset=utf8";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ];
        try {
            self::$pdo = new PDO($dsn, $this->user, $this->password, $options);

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
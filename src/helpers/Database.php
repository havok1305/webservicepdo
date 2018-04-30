<?php

class Database
{
    protected $db;
    protected $user;
    protected $password;
    protected $host;
    protected $pdo;

    public function __construct()
    {
        $this->host = 'localhost';
        $this->db = 'webservice';
        $this->user = 'root';
        $this->password = 'root';
        $dsn = "mysql:dbname={$this->db};host={$this->host}";

        try {
            $this->pdo = new PDO($dsn, $this->user, $this->password);
        } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
        }
    }
    public function getPdo() {
        return $this->pdo;
    }

}
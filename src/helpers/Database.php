<?php

class Database
{
    protected $dbname;
    protected $user;
    protected $password;
    protected $host;
    private static $pdo;

    public function __construct()
    {
        $this->host = getenv('DB_HOST');
        $this->dbname = getenv('DB_NAME');
        $this->user = getenv('DB_USER');
        $this->password = getenv('DB_PASSWORD');

        $dsn = "mysql:dbname={$this->dbname};host={$this->host};charset=utf8";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ];
        try {
            self::$pdo = new PDO($dsn, $this->user, $this->password, $options);

        } catch (PDOException $e) {
//            echo 'Connection failed: ' . $e->getMessage();
        }
    }

    public static function getInstance()
    {
        if(!isset(self::$pdo)) {
            new Database();
        }
        return self::$pdo;
    }

    public static function destroyInstance()
    {
        if(isset(self::$pdo)) {
            self::$pdo = null;
        }
    }

}
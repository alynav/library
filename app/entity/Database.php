<?php


class Database
{
    private $dbServername = 'mysql';
    private $dbUsername = 'root';
    private $dbPassword = 'biblioteca';
    private $dbName = 'biblioteca';
    private $port = 3306;
    private $connection;

    private static $instance = null;

    private function __construct()
    {
        $this->connection =
            mysqli_connect($this->dbServername, $this->dbUsername, $this->dbPassword, $this->dbName, $this->port);
    }

    public static function getInstance()
    {
        if (self::$instance == null){
            self::$instance = new Database();
        }

        return self::$instance;
    }

    public function insert($query)
    {
        $this->connection->query($query);

        return mysqli_insert_id($this->connection);
    }

    public function query($query)
    {
        return $this->connection->query($query);
    }

    public function escapeString(string $value)
    {
        return $this->connection->real_escape_string($value);
    }
}
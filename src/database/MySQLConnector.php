<?php

namespace App\Database;

use PDO;
use App\Lib\Database\DatabaseAdapter;

/**
 * Class MySQLConnector
 * @package Src\Database
 */
class MySQLConnector implements DatabaseAdapter
{

    private static ?DatabaseAdapter $instance = null;

    private PDO $connection;

    private function __construct()
    {
        $this->connect();
    }

    public static function getInstance()
    {
        if (self::$instance === null)
        {
            self::$instance = new MySQLConnector();
        }

        return self::$instance;
    }

    /**
     * Private helper to instantiate a new MYSQL PDO
     */
    public function connect()
    {
        $dbHost       = getenv('DB_HOST') ?? 'localhost';
        $dbPort       = getenv('DB_PORT') ?? '3306';
        $dbName     = getenv('DB_NAME') ?? 'rest-api';
        $dbUsername = getenv('DB_USER') ?? 'root';
        $dbPassword = getenv('DB_PASSWORD') ?? '';

        try
        {
            $this->connection = new PDO("mysql:host=$dbHost;dbPort=$dbPort;dbname=$dbName;charset=UTF8",$dbUsername,$dbPassword);
        }
        catch (\PDOException $e)
        {
            throw $e;
        }
    }

    /**
     * @return PDO
     */
    public function getConnection()
    {
        return $this->connection;
    }
}
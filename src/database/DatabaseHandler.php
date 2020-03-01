<?php

namespace App\Database;

use PDO;
use App\Lib\Database\DatabaseAdapter;

class DatabaseHandler
{
    public DatabaseAdapter $engine;

    public function __construct()
    {
        switch (getenv('DB_ENGINE'))
        {
            case 'mysql':
                $this->engine = MySQLConnector::getInstance();
                break;
            case 'pgsql':
                throw new \Exception('Database Engine ' . getenv('DB_ENGINE') . ' not implemented yet');
            case 'sqlserver':
                throw new \Exception('Database Engine ' . getenv('DB_ENGINE') . ' not implemented yet');
            default:
                throw new \Exception('Database Engine ' . getenv('DB_ENGINE') . ' not supported');
        }
    }

    /**
     * @return PDO
     */
    public function connection()
    {
        return $this->engine->getConnection();
    }
}
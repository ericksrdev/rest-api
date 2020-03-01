<?php

namespace App\Lib;

use PDO;

abstract class RootModel
{
    private array $attributes = [];

    protected static string $table;

    protected static PDO $connection;

    protected bool $saved;

    public function __construct($connection)
    {
        if ($connection === null)
        {
            throw new \Exception('Database connection is not available');
        }

        $this->primaryKey = 'id';

        $this->table = $this->table === null ? Pluralizer::pluralize(strtolower(__CLASS__)) : $this->table;
    }

    /**
     * Finds a element of the entity by primaryKey
     * by default primaryKey is set to 'id', the primaryKey must be an int
     * @param int $id
     * @return mixed
     */
    public static function find(int $id)
    {
        $statement = "SELECT * FROM " . self::$table . " WHERE id = :PK";

        $statement = self::$connection->prepare($statement);

        $statement->execute([
                                'PK' => $id,
                            ]);

        $result = $statement->fetch(PDO::FETCH_CLASS, self::class);

        return $result;
    }

    /**
     * Finds all the elements of the entity
     * @return array
     */
    public static function findAll()
    {
        $statement = "SELECT * FROM " . self::$table;

        $statement = self::$connection->prepare($statement);

        $statement->execute();

        $result = $statement->fetchAll(PDO::FETCH_CLASS, self::class);

        return $result;
    }

    /**
     * @param $column   string The table column
     * @param $operator string valid operators: =,<>, LIKE
     * @param $value    mixed a value to search by
     */
    public function findWhere(string $column, string $operator = null, $value)
    {
        if ($operator === null)
        {
            $operator = '=';
        }

        $statement = "SELECT * FROM $this->table WHERE ? " . $operator . " ?";

        $statement = $this->connection->prepare($statement);

        $statement->execute([
                                $column,
                                $value
                            ]);

        $result = $statement->fetchAll(PDO::FETCH_CLASS, __CLASS__);

        return $result;
    }

    public function save()
    {
        $query = '';
        /**
         * If a primary key attribute is set, this is an update
         */

        $query = QueryBuilder::buildModelQuery(
            $this->attributes,
            ! isset($this->attributes[$this->primaryKey])
                ?
                QueryBuilder::INSERT_QUERY_TYPE
                :
                QueryBuilder::UPDATE_QUERY_TYPE
        );

        $parsedAttributes = QueryBuilder::parseModelAttributes($this->attributes);

        $statement = $this->connection->prepare($query);

        $statement->execute($parsedAttributes);

        return (bool) $statement->rowCount();
    }

    public function delete()
    {

    }

    public function __set($name, $value)
    {
        $this->attributes[$name] = $value;

        $this->saved = false;
    }

    public function __get($name)
    {
        return $this->attributes[$name] ?? null;
    }
}
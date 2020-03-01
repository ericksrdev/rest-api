<?php

namespace App\Lib;

use App\Database\DatabaseHandler;
use App\Lib\Database\QueryBuilder;
use PDO;

abstract class RootModel
{
    protected array $attributes = [];

    protected static ?string $table = null;

    protected static array $relations = [];

    protected static ?PDO $connection = null;

    protected bool $saved;

    public function __construct($connection = null)
    {
        if (self::$connection === null)
        {
            self::$connection = (new DatabaseHandler())->connection();
        }

        $this->primaryKey = 'id';

        $this->table = $this->table === null ? Pluralizer::pluralize(strtolower(get_called_class())) : $this->table;
    }

    /**
     * Finds a element of the entity by primaryKey
     * by default primaryKey is set to 'id', the primaryKey must be an int
     * @param int $id
     * @param $table
     * @return mixed
     */
    public static function find(int $id, $table = null)
    {
        static::bootIfNotBooted();

        $statement = "SELECT * FROM " . static::$table . " WHERE id = :PK";

        $statement = static::$connection->prepare($statement);

        $statement->execute([
                                'PK' => $id,
                            ]);

        $result = $statement->fetchObject(get_called_class());

        if ( ! $result)
        {
            return null;
        }

        static::prepareFetchRelationships($result);

        return $result;
    }

    /**
     * Finds all the elements of the entity
     * @return array
     */
    public static function findAll()
    {
        static::bootIfNotBooted();

        $statement = "SELECT * FROM " . static::$table;

        $statement = static::$connection->prepare($statement);

        $statement->execute();

        $statement->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $result = $statement->fetchAll();

        static::prepareFetchRelationships($result);

        return $result;
    }

    /**
     * @param string $column
     * @param string|null $operator
     * @param $value
     * @return array
     */
    public static function findWhere(string $column, string $operator = '=', $value = null)
    {
        static::bootIfNotBooted();

        if ($operator === null)
        {
            $operator = '=';
        }

        $statement = "SELECT * FROM " . static::$table . " WHERE ? " . $operator . " ?";

        $statement = static::$connection->prepare($statement);

        $statement->execute([
                                $column,
                                $value,
                            ]);

        $result = $statement->fetchAll(PDO::FETCH_CLASS, get_called_class());

        return $result;
    }

    public static function findWhereIn(string $column, array $values)
    {
        static::bootIfNotBooted();

        $statement = "SELECT * FROM " . static::$table . " WHERE " . $column . " IN (" . implode(',', $values) . ")";

        $statement = static::$connection->prepare($statement);

        $statement->execute([]);

        $result = $statement->fetchAll(PDO::FETCH_CLASS, get_called_class());

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

    private static function prepareFetchRelationships($result)
    {
        //Fetching collection relationships
        if (count(static::$relations) > 0)
        {
            foreach (static::$relations as $relation)
            {
                if (is_array($result))
                {
                    foreach ($result as &$item)
                    {
                        $item->{$relation['name']} = static::fetchCollectionRelationships($relation, [$item->toArray()]);
                    }
                }
                else
                {
                    $result->{$relation['name']} = static::fetchCollectionRelationships($relation, [$result->toArray()]);
                }

            }

        }
    }

    private static function fetchCollectionRelationships(array $relation, $resultCollection)
    {
        $localKeyValues = array_column($resultCollection, $relation['local_key']);

        foreach ($localKeyValues as &$localKeyValue)
        {
            if (is_numeric($localKeyValue))
            {
                $localKeyValue = (int) $localKeyValue;
            }
        }

        if ($relation['type'] === 'hasMany')
        {

            $relationQueryResult = $relation['class_reference']::findWhereIn($relation['foreign_key'], $localKeyValues);
        }

        return $relationQueryResult;
    }

    private static function bootIfNotBooted()
    {
        if (static::$connection === null)
        {
            static::$connection = (new DatabaseHandler())->connection();
        }
    }

    public function toArray()
    {
        foreach ($this->attributes as $key => &$val)
        {
            if (in_array($key, array_column(static::$relations, 'name')))
            {
                if (is_array($val))
                {
                    foreach ($val as &$relatedAttribute)
                    {
                        $relatedAttribute = $relatedAttribute->toArray();
                    }
                }
                elseif (is_object($val) && is_subclass_of($val, RootModel::class))
                {
                    $val->toArray();
                }
            }
        }

        return $this->attributes;
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

    public function __toString()
    {
        return json_encode($this->attributes);
    }
    
}
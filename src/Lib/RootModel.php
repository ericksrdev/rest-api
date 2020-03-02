<?php

namespace App\Lib;

use App\Database\DatabaseHandler;
use App\Lib\Database\QueryBuilder;
use PDO;
use phpDocumentor\Reflection\Types\Static_;
use function PHPUnit\Framework\containsOnly;

abstract class RootModel
{
    protected array $attributes = [];

    protected array $hidden = [];

    private static array $nonMutable = ['table', 'primaryKey', 'created_at', 'updated_at'];

    protected static ?string $table = null;

    protected static array $relations = [];

    protected static ?PDO $connection = null;

    protected bool $saved;

    protected static string $primaryKey;

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
     * @param $disableIntrospection bool Disables child relations lookup
     * @return mixed
     */
    public static function find(int $id, $disableIntrospection = false)
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

        if ( ! $disableIntrospection)
        {
            static::prepareFetchRelationships($result);
        }

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

        $statement = "SELECT * FROM " . static::$table . " WHERE $column " . $operator . " ?";

        $statement = static::$connection->prepare($statement);

        $statement->execute([
                                $value,
                            ]);

        $result = $statement->fetchAll(PDO::FETCH_CLASS, get_called_class());

        return $result;
    }

    /**
     * Process many where clauses using an array
     * [
     *  [
     *    $column,
     *    {Operator (AND|OR)},
     *    $value
     *  ]
     * ]
     *
     * @param array $where
     * @param string $operator (AND,OR)
     * @param bool $disableIntrospection
     * @return array
     */
    public static function findWhereMany(array $where, $operator = 'AND', $disableIntrospection = true)
    {
        static::bootIfNotBooted();

        $statement = "SELECT * FROM " . static::$table . " WHERE ";

        $values = [];

        foreach ($where as $whereClause)
        {

            if (count($whereClause) === 3)
            {
                if (count($values) > 0)
                {
                    $statement .= "$operator $whereClause[0] $whereClause[1] ? ";
                }
                else
                {
                    $statement .= "$whereClause[0] $whereClause[1] ? ";
                }

                $values[] = $whereClause[2];
            }
        }

        $statement = static::$connection->prepare($statement);

        $statement->execute($values);

        $result = $statement->fetchAll(PDO::FETCH_CLASS, get_called_class());

        if ( ! $disableIntrospection)
        {
            static::prepareFetchRelationships($result);
        }

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
        static::bootIfNotBooted();
        $query = '';
        /**
         * If a primary key attribute is set, this is an update
         */

        $query = QueryBuilder::buildModelQuery(
            $this->getMutableParameters(),
            ! isset($this->attributes['id'])
                ?
                QueryBuilder::INSERT_QUERY_TYPE
                :
                QueryBuilder::UPDATE_QUERY_TYPE,
            static::$table,
            $this->attributes['id'] ?? null
        );

        $parsedAttributes = QueryBuilder::parseModelAttributes($this->getMutableParameters());

        $statement = static::$connection->prepare($query);

        $statement->execute($parsedAttributes);

        $rowCount = $statement->rowCount();

        if ($rowCount)
        {
            $this->id = $this->id === null ? (int) static::$connection->lastInsertId() : $this->id;
        }

        return (bool) $rowCount;
    }

    public function delete()
    {
        static::bootIfNotBooted();

        $statement = "DELETE FROM " . static::$table . " WHERE id = :ID";

        $statement = static::$connection->prepare($statement);

        $statement->execute([
                                ':ID' => $this->attributes['id'],
                            ]);

        return (bool) $statement->rowCount();
    }

    private static function prepareFetchRelationships($result)
    {
        //Fetching collection relationships

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

        switch ($relation['type'])
        {
            case 'hasMany':
                $relationQueryResult = $relation['class_reference']::findWhereIn($relation['foreign_key'], $localKeyValues);
                break;
            case 'belongsTo':
            case 'hasOne':
                $relationQueryResult = $relation['class_reference']::find($localKeyValues[0], true);
                break;
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

    private function getMutableParameters()
    {
        $params = [];

        foreach ($this->attributes as $key => $val)
        {
            if ($key == 'id')
            {
                continue;
            }
            if ( ! in_array($key, static::$nonMutable) && ! in_array($key, array_column(static::$relations, 'name')))
            {
                $params[$key] = $val;
            }
        }

        return $params;
    }

    public function toArray()
    {
        foreach ($this->attributes as $key => &$val)
        {
            if (in_array($key, static::$nonMutable) || in_array($key, $this->hidden))
            {
                continue;
            }

            if ($key == 'id')
            {
                $val = (int) $val;
            }

            if (in_array($key, array_column(static::$relations, 'name')))
            {
                if (is_array($val))
                {
                    foreach ($val as &$relatedAttribute)
                    {

                        if (is_array($relatedAttribute))
                        {
                            foreach ($relatedAttribute as $key => &$val)
                            {
                                if (is_object($val))
                                {
                                    $val = $val->toArray();
                                }
                            }
                        }
                        if (is_object($relatedAttribute))
                        {
                            $relatedAttribute = $relatedAttribute->toArray();
                        }
                    }
                }

                if (is_object($val))
                {
                    $val->toArray();
                }
            }
        }

        return $this->attributes;
    }

    public function __set($name, $value)
    {
        if ($name !== 'table' && $name !== 'primaryKey')
        {
            $this->attributes[$name] = $value;

            $this->saved = false;
        }
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
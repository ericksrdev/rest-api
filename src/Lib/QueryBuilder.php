<?php

namespace App\Lib;

class QueryBuilder
{
    public const INSERT_QUERY_TYPE = 'insert';
    public const UPDATE_QUERY_TYPE = 'update';

    /**
     * Creates an insert or update query for models
     *
     * @param array $attributes
     * @param string $type
     * @return string
     */
    public static function buildModelQuery(array $attributes, string $type)
    {
        $sql = "";

        $attributeNames = [];

        foreach ($attributes as $key => $val)
        {
            if (strtolower($key) !== 'id')
            {
                $attributeNames[] = strtoupper($key);
            }
        }

        switch ($type)
        {
            case self::INSERT_QUERY_TYPE:
                $sql .= "INSERT INTO :TABLE_NAME (" . implode(',', array_map('strtolower', $attributeNames)) . ")";

                $attributeNames = array_map(fn ($item) => ":$item", $attributeNames);

                $sql .= " VALUES (" . implode(',', $attributeNames). ")";

                break;
            case self::UPDATE_QUERY_TYPE:
                $sql .= "UPDATE :TABLE_NAME SET";
                foreach ($attributeNames as $attributeName)
                {
                    $sql .= " " . strtolower($attributeName) . " = :$attributeName,";
                }

                $sql = rtrim($sql, ',');

                $sql .= " WHERE id = :PRIMARY_KEY";
                break;
        }

        return $sql;
    }

    /**
     * Parses the model attributes to work with pdo
     * @param array $attributes
     * @return array
     */
    public static function parseModelAttributes(array $attributes)
    {
        $parsedArray = [];

        foreach ($attributes as $key => $val)
        {
            $parsedArray[":$key"] = $val;
        }

        return $parsedArray;
    }

}
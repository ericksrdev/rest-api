<?php

namespace App\Models;

class Phone extends Model
{
    protected static ?string $table = "phones";

    public static function findAll($table = null)
    {
        return parent::findAll(self::$table);
    }

    public static function find(int $id, $table = null)
    {
        return parent::find($id, self::$table);
    }
}
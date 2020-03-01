<?php

namespace App\Models;

class User extends Model
{
    protected static ?string $table = "users";

    protected static array $relations = [
        ['name' => 'emails', 'type' => 'hasMany', 'local_key' => 'id', 'foreign_key' => 'user_id', 'class_reference' => Email::class],
        ['name' => 'phones', 'type' => 'hasMany', 'local_key' => 'id', 'foreign_key' => 'user_id', 'class_reference' => Phone::class],
    ];

}
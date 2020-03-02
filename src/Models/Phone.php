<?php

namespace App\Models;

class Phone extends Model
{
    protected static ?string $table = "phones";

    protected static array $relations =
        ['name' => 'user', 'type' => 'belongsTo', 'local_key' => 'user_id', 'foreign_key' => 'id', 'class_reference' => User::class];
}

<?php

namespace App\Lib\Database;

interface DatabaseAdapter
{
    public static function getInstance();

    public function getConnection();

    function connect();
}
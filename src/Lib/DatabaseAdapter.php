<?php

namespace App\Lib;

interface DatabaseAdapter
{
    public static function getInstance();

    public function getConnection();

    function connect();
}
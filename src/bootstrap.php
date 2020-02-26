<?php

require 'vendor/autoload.php';
use Dotenv\Dotenv;
use Src\database\DatabaseHandler;

$dotenv = new Dotenv(__DIR__);

$dotenv->load();

$dbConnection = (new Src\Database\DatabaseHandler())->getConnection();


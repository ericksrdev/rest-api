<?php
error_reporting(E_ERROR | E_PARSE);

require 'vendor/autoload.php';

use App\Database\DatabaseHandler;
use App\Lib\AppDispatcher;
use Dotenv\Dotenv;

//Loading .env vars to php env

$dirArray = explode('/', __DIR__);

$rootDir = implode('/', array_slice($dirArray, 0, count($dirArray) - 1));

try
{
    (new Dotenv($rootDir))->load();
}
catch (Exception $e)
{
    //Running with prod .env values
}

$dbConnection = (new DatabaseHandler())->connection();

$app = new AppDispatcher();

$HttpQuest = new \HTTPQuest\HTTPQuest();
$HttpQuest->decode($_POST, $_FILES);

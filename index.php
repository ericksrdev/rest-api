<?php
//bootstrapping application
require 'src/bootstrap.php';



//Retrieving needed parameters

$baseURI = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$explodedURI = explode('/', $baseURI);

$requestParameters = array_merge($_POST, $_GET, $_FILES);

$app->handle($baseURI, $explodedURI, $_SERVER['REQUEST_METHOD'], $requestParameters);
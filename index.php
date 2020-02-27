<?php
//bootstrapping application
require 'src/bootstrap.php';

//Retrieving needed parameters

$baseURI = parse_url($HTTP_SERVER_VARS['REQUEST_URI'], PHP_URL_PATH);

$explodedUri = explode('/', $baseURI);

$requestParameters = $_REQUEST;

$app->handle($baseURI, $explodedUri, $HTTP_SERVER_VARS['REQUEST_METHOD'], $requestParameters);
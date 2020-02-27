<?php

namespace App\Lib;

class AppDispatcher
{
    private array $routes;

    public function __construct()
    {
        $this->routes = include '../Routes/Routes.php';
    }

    public function handle(string $baseURI, array $requestURI, string $requestMethod, array $requestParams)
    {
        var_dump($baseURI);
        echo PHP_EOL.'------------------------'.PHP_EOL;
        var_dump($requestURI);
        echo PHP_EOL.'------------------------'.PHP_EOL;
        var_dump($requestMethod);
        echo PHP_EOL.'------------------------'.PHP_EOL;
        var_dump($requestParams);
        echo PHP_EOL.'------------------------'.PHP_EOL;
    }
}
<?php

namespace App\Lib;

use App\Lib\Exceptions\HttpException;
use App\Lib\Http\RequestValidator;
use App\Lib\Routing\Routes;

/**
 * Front controller of the application, this class is responsible for handling all the request lifecycle
 *
 * Class AppDispatcher
 * @package App\Lib
 * @author  Erick Sandoval
 */
class AppDispatcher
{

    public function __construct()
    {
        //Booting the routing system and loads user defined routes from routes.php file
        Routes::boot();
    }

    /**
     * Handles all requests coming to index.php
     *
     * @param string $baseURI
     * @param array $requestURI
     * @param string $requestMethod
     * @param array|null $requestParams
     * @throws Exceptions\HttpException
     * @throws Exceptions\RouteException
     */
    public function handle(string $baseURI, array $requestURI, string $requestMethod, ?array $requestParams)
    {

        try
        {
            $currentRequestAction = RequestValidator::processRequest($requestURI, $requestMethod);


            $invoker = new Invoker($requestParams,$currentRequestAction['params']);

            $response = $invoker->callAction($currentRequestAction['controllerClass'], $currentRequestAction['controllerMethod']);

            $this->finish($response);
        }
        catch (HttpException | \Exception $e)
        {
            echo $e->getMessage();
        }
    }

    private function finish($data = null)
    {
        echo $data;
    }
}
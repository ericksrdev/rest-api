<?php

namespace App\Lib\Http;

use App\Lib\Exceptions\HttpException;
use App\Lib\Exceptions\RouteException;
use App\Lib\Routing\Routes;

class RequestValidator
{

    /**
     * Searches for a route definition, if exists it returns controller and action to be executed
     *
     * @param array $uri
     * @param string $method
     * @return mixed|null
     * @throws HttpException
     * @throws RouteException
     */
    public static function processRequest(array $uri, string $method)
    {

        //This represents a root request to index
        if (count($uri) == 2 && strlen($uri[0]) == 0 && strlen($uri[1]) == 0)
        {
            $uri = ["/"];
        }

        $routeInQuestion = Routes::search($uri, $method);

        if ($routeInQuestion === null)
        {
            throw new HttpException("Not Found", 404);
        }

        return $routeInQuestion;
    }

}
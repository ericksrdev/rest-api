<?php

namespace App\Lib\Routing;

use App\Lib\Exceptions\RouteException;
use Prophecy\Exception\Doubler\MethodNotFoundException;

/**
 * Class Routes
 * @method
 * @package App\Lib\Routing
 */
class Routes
{
    public static array $routes = [];

    public static array $verbs = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];

    public static function addRoute($route, $method, $action)
    {
        self::validateRoute($route, $action);

        self::$routes[] = [
            'route'  => $route,
            'method' => $method,
            'action' => $action,
        ];
    }

    private static function validateRoute($route, $action)
    {
        $actionSplited = explode('@', $action);

        if (count($actionSplited) !== 2)
        {
            throw new RouteException("Invalid action for route $route");
        }

        $targetClass = $actionSplited[0];

        $targetMethod = $actionSplited[1];

        if ( ! class_exists($targetClass, true))
        {
            throw new RouteException("Unable to find controller class  $targetClass");
        }

        $instance = new $targetClass;

        if ( ! method_exists($instance, $targetMethod))
        {
            throw new RouteException("Unable to find controller action $targetClass @  $targetMethod");
        }

    }

    public static function __callStatic($name, $arguments)
    {
        if(! in_array(strtoupper($name),self::$verbs)){
            throw new MethodNotFoundException();
        }

    }
}
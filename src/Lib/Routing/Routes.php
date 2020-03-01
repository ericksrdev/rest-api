<?php

namespace App\Lib\Routing;

use App\Lib\Exceptions\RouteException;
use SebastianBergmann\CodeCoverage\Report\PHP;

/**
 * Class Routes
 * Routes mapper and administrator
 * @package App\Lib\Routing
 */
class Routes implements RoutingInterface
{
    public static array $routes = [];

    public static array $verbs = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];

    private static string $namespace = '\\App\\Controllers\\';

    private static string $prefix = '';

    public static function get($route, $action)
    {
        self::addRoute($route, 'GET', $action);
    }

    public static function post($route, $action)
    {
        self::addRoute($route, 'POST', $action);
    }

    public static function put($route, $action)
    {
        self::addRoute($route, 'PUT', $action);
    }

    public static function patch($route, $action)
    {
        self::addRoute($route, 'PATCH', $action);
    }

    public static function delete($route, $action)
    {
        self::addRoute($route, 'DELETE', $action);
    }

    /**
     * Sets a new prefix for the routes
     * @param string $prefix
     */
    public static function setPrefix(string $prefix)
    {
        self::$prefix = $prefix;
    }

    /**
     * Searches a route to match request
     *
     * @param $targetRoute array
     * @param $method
     * @return mixed|null
     * @throws RouteException
     */
    public static function search(array $targetRoute, $method)
    {
        if ( ! in_array($method, self::$verbs))
        {
            throw new RouteException("Unsupported HTTP $method method");
        }

        if (count(self::$routes) == 0)
        {
            return;
        }

        foreach (self::$routes as $route)
        {
            if ($route['method'] === $method)
            {
                $splittedRoute = explode('/', $route['route']);

                if (count($splittedRoute) == 2 && strlen($splittedRoute[0]) == 0 && strlen($splittedRoute[1]) == 0)
                {
                    $splittedRoute = ["/"];
                }

                $splittedRoute = array_values(array_filter($splittedRoute, fn ($item) => $item !== ""));

                $targetRoute = array_values(array_filter($targetRoute, fn ($item) => $item !== ""));

                if (self::compareRoute($splittedRoute, $targetRoute) === false)
                {
                    continue;
                }

                //At this point the $route contains the requested route
                //This section will attach the controller class with the base namespace in an attribute
                //and  the controller method in another attribute

                $actionSplitted = explode('@', $route['action']);

                $route['controllerClass'] = self::$namespace . $actionSplitted[0];

                $route['controllerMethod'] = $actionSplitted[1];

                //This will attach the route url parameters matched with the requested values
                //i.e /users/{user_id} /user/1 this will add a parameters assoc attribute to the array containing the key =>value i.e user_id => 1
                $routeParams = self::getRouteParameters($splittedRoute, $targetRoute);

                $route['params'] = $routeParams;

                return $route;
            }
        }

        return null;
    }

    private static function addRoute($route, $method, $action)
    {
        self::validateRoute($route, $action, $method);

        self::$routes[] = [
            'route'  => $route,
            'method' => $method,
            'action' => $action,
        ];
    }

    /**
     * Verifies if the route is valid and if it's not already declared
     *
     * @param $route  string route in question
     * @param $action string route action in question
     * @param $method string route action http method
     * @throws RouteException
     */
    private static function validateRoute($route, $action, $method)
    {
        $actionSplitted = explode('@', $action);

        //Validates if the route already exists
        if (self::search(explode('/', $route), $method) !== null)
        {
            throw new RouteException("A duplicated route entry has been found for $route and $method method");
        }

        //Validates if the prefix is set correctly
        if ( ! empty(self::$prefix))
        {
            if ( ! self::$prefix[0] === '/')
            {
                throw new RouteException('Prefix string must start with right slash i.e. /api/v1');
            }
        }

        //Validates if the route action has a @ in the middle
        if (count($actionSplitted) !== 2)
        {
            throw new RouteException("Invalid action for route $route");
        }

        //Retrieves the route action controller class
        $targetClass = self::$namespace . $actionSplitted[0];

        //Retrieves the controller's action method string name
        $targetMethod = $actionSplitted[1];

        //Verifies for class been defined
        if ( ! class_exists($targetClass))
        {
            throw new RouteException("Unable to find controller class  $targetClass");
        }

        //Creates a new instance of the controller's class
        $instance = new $targetClass;

        //Verifies if the controller's action method exists
        if ( ! method_exists($instance, $targetMethod))
        {
            throw new RouteException("Unable to find controller action $targetClass @  $targetMethod");
        }

        //Trashing the controller's instance
        unset($instance);
    }

    /**
     * Compares a stored route with a provided target route
     * @param array $storedRoute
     * @param array $targetedRoute
     * @return bool
     */
    private static function compareRoute(array $storedRoute, array $targetedRoute)
    {
        //If the routes doesn't have the same length it won't match
        if (count($storedRoute) !== count($targetedRoute))
        {
            return false;
        }

        //This array will walk throug both rotues to find matches at every position
        for ($i = 0; $i < count($storedRoute); $i++)
        {

            //If the comparison fails could be caused by a route parameter
            if ($storedRoute[$i] !== $targetedRoute[$i])
            {
                //Validating if the stored route has a URL parameter like /users/{user_id} which means if the route has this it must pass it and continue evaluation
                if (strpos($storedRoute[$i], '{') !== false && strpos($storedRoute[$i], '}') !== false)
                {
                    continue;
                }

                //If the script leads here it means the route is not found
                return false;
            }
        }
        return true;
    }

    /**
     * Returns an assoc array containing all the route params
     *
     * @param array $storedRoute
     * @param array $targetedRoute
     * @return array
     */
    private static function getRouteParameters(array $storedRoute, array $targetedRoute)
    {
        $routeParameters = [];

        for ($i = 0; $i < count($storedRoute); $i++)
        {
            if (strpos($storedRoute[$i], '{') !== false && strpos($storedRoute[$i], '}') !== false)
            {
                $paramKey = trim($storedRoute[$i], '{}');
                $paramVal = $targetedRoute[$i];

                $routeParameters[$paramKey] = $paramVal;
            }
        }

        return $routeParameters;
    }

    /**
     * Loads user defined routes
     */
    public static function boot()
    {
        include('src/Routes/routes.php');
    }

    /**
     * This class can't be instantiated
     * Routes constructor.
     */
    private function __construct()
    {
    }
}
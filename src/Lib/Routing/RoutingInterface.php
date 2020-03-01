<?php

namespace App\Lib\Routing;

interface RoutingInterface
{
    /**
     * Maps a new get route
     *
     * @param $route
     * @param $action
     */
    public static function get($route, $action);

    /**
     * Maps a new post route
     *
     * @param $route
     * @param $action
     */
    public static function post($route, $action);

    /**
     * Maps a new put route
     *
     * @param $route
     * @param $action
     */
    public static function put($route, $action);

    /**
     * Maps a new patch route
     *
     * @param $route
     * @param $action
     */
    public static function patch($route, $action);

    /**
     * Maps a new delete route
     *
     * @param $route
     * @param $action
     */
    public static function delete($route, $action);
}
<?php

namespace App\Lib;

use App\Lib\Http\Request;

class Invoker
{
    private Request $request;

    public function __construct(array $attributes = [], array $urlAttributes = [])
    {
        $uuid          = uniqid('request_');
        $attributes = array_merge($attributes, $urlAttributes);
        $this->request = new Request($uuid, $attributes);
    }

    public function callAction(string $callableClass, string $callableMethod)
    {

        $controller = new $callableClass();

        $result = $controller->$callableMethod($this->request);

        return $result;
    }
}
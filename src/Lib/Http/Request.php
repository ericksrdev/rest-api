<?php

namespace App\Lib\Http;

class Request
{
    public string $id;

    private array $parameters = [];

    public function __construct($id, $serverParameters)
    {
        $this->id         = $id;
        $this->parameters = $serverParameters;
    }

    /**
     * Retrieves a HTTP parameter of the current request
     *
     * @param $key
     * @return mixed|null
     */
    public function get($key)
    {
        if ( ! array_key_exists($key, $this->parameters))
        {
            return null;
        }

        return $this->parameters[$key];
    }

    public function all()
    {
        return $this->parameters;
    }
}
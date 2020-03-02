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

    public function has($key)
    {
        return array_key_exists($key, $this->parameters);
    }

    /**
     * Retrieves all the request parameters
     * @return array
     */
    public function all()
    {
        return $this->parameters;
    }

    /**
     * Validates if the parameter is a valid file
     * @param $key
     * @return bool
     */
    public function isValidFile($key)
    {
        return array_key_exists($key, $this->parameters) &&
               array_key_exists('size', $this->parameters[$key]) &&
               $this->parameters[$key]['error'] == 0;
    }

    /**
     * Validates the current request to contain the required parameters
     * @param array $requiredParameters Unidirectional list of parameter names i.e. ['name', 'last_name']
     * @return bool
     */
    public function validateRequired(array $requiredParameters)
    {
        foreach ($requiredParameters as $requiredParameter)
        {
            if ( ! array_key_exists($requiredParameter, $this->parameters))
            {
                return false;
            }
        }

        return true;
    }
}
<?php

namespace App\Lib\Exceptions;

use Throwable;

class RouteException extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null) { parent::__construct($message, $code, $previous); }
}
<?php

namespace App\Lib\Exceptions;

use Throwable;

class HttpException extends \Exception
{
    protected int $httpStatus = 400;

    public function __construct($message = "", $httpStatus = 400, $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        header("HTTP/1.0 $httpStatus $message");
    }

}
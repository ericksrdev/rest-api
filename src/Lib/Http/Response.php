<?php

namespace App\Lib\Http;

class Response implements ResponseContract
{
    public function json(array $data = [], int $statusCode = 200)
    {
        header("HTTP/1.0 $statusCode");
        header('Content-Type: application/json');
        return json_encode($data);
    }

    public function text(string $text = '', int $statusCode = 200)
    {
        header("HTTP/1.0 $statusCode");
        header("Content-Type: text/plain");

        return $text;
    }

    public function html(string $html, int $statusCode = 200)
    {
        header("Content-Type: text/html");
        return $html;
    }

    public function empty(int $statusCode)
    {
        header("HTTP/1.0 $statusCode");
        return null;
    }
}
<?php

namespace App\Lib\Http;

use App\Lib\RootModel;

class Response implements ResponseContract
{
    /**
     * @param mixed $data
     * @param int $statusCode
     * @return false|mixed|string
     */
    public function json($data = null, int $statusCode = 200)
    {
        header("HTTP/1.0 $statusCode");
        header('Content-Type: application/json');

        if (is_object($data) && is_subclass_of($data, RootModel::class))
        {
            $data = $data->toArray();
        }

        if (is_array($data))
        {
            foreach ($data as &$d)
            {
                if (is_object($d) && is_subclass_of($d, RootModel::class))
                {
                    $d = $d->toArray();
                }
            }
        }

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
<?php

namespace App\Lib\Http;

interface ResponseContract
{
    /**
     * Sends a JSON formatted response with application/json header set
     *
     * @param mixed $data     array Data to be parsed to json
     * @param int $statusCode int HTTP status code
     * @return mixed
     */
    public function json($data = null, int $statusCode = 200);

    /**
     * Sends a text formatted response with plain text header set
     * @param string $text
     * @param int $statusCode
     * @return mixed
     */
    public function text(string $text = '', int $statusCode = 200);

    /**
     * Sends a html formatted response with html header set
     * @param string $html
     * @param int $statusCode
     * @return mixed
     */
    public function html(string $html, int $statusCode = 200);

    /**
     * Sends a response with no body
     * @param int $statusCode
     * @return mixed
     */
    public function empty(int $statusCode);
}
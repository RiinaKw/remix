<?php

namespace Remix\Exceptions\Http;

use Remix\Exceptions\HttpException;
use Utility\Http\StatusCode;

/**
 * HTTP Exception class indicating the "404 Not Found"
 *
 * @package  Remix\Exception\Http
 */
class HttpNotFoundException extends HttpException
{
    /**
     * constructor
     * @param string $message  Message of Exception
     */
    public function __construct(string $message)
    {
        parent::__construct($message, StatusCode::NOT_FOUND);
    }
    // function __construct()
}
// class HttpNotFoundException

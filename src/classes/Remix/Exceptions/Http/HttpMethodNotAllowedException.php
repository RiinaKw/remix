<?php

namespace Remix\Exceptions\Http;

use Remix\Exceptions\HttpException;
use Utility\Http\StatusCode;

/**
 * HTTP Exception class indicating the "405 Method Not Allowed"
 *
 * @package  Remix\Exceptions\Http
 */
class HttpMethodNotAllowedException extends HttpException
{
    /**
     * constructor
     * @param string $message  Message of Exception
     */
    public function __construct(string $message)
    {
        parent::__construct($message, StatusCode::METHOD_NOT_ALLOWED);
    }
    // function __construct()
}
// class HttpNotFoundException

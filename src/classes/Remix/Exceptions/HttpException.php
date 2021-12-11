<?php

namespace Remix\Exceptions;

use Utility\Http\StatusCode;

/**
 * Exception class indicating the HTTP error.
 *
 * @package  Remix\Exceptions
 */
class HttpException extends \Remix\RemixException
{
    /**
     * HTTP status code
     * @var int
     */
    protected $status_code = StatusCode::INTERNAL_SERVER_ERROR;

    /**
     * constructor
     * @param string $message     Message of Exception
     * @param int    $status_code  HTTP status code
     */
    public function __construct(string $message, int $status_code = StatusCode::INTERNAL_SERVER_ERROR)
    {
        parent::__construct($message);
        $this->status_code = $status_code;
    }
    // function __construct()

    /**
     * Get HTTP status code.
     * @return int  HTTP status code
     */
    public function getStatusCode(): int
    {
        return $this->status_code;
    }
    // function getStatus()
}
// class HttpException

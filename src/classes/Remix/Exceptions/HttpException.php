<?php

namespace Remix\Exceptions;

/**
 * Exception class indicating the HTTP error
 *
 * @package  Remix\Exception
 */
class HttpException extends \Remix\RemixException
{
    protected $status_code = 200;

    public function __construct(string $message, int $status_code = 200)
    {
        parent::__construct($message);
        $this->status_code = $status_code;
    }
    // function __construct()

    public function getStatusCode(): int
    {
        return $this->status_code;
    }
    // function getStatus()
}
// class HttpException

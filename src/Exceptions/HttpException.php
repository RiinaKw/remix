<?php

namespace Remix\Exceptions;

class HttpException extends \Remix\RemixException
{
    protected $status = 200;

    public function __construct(string $message, int $status = 200)
    {
        parent::__construct($message);
        $this->status = $status;
    } // function __construct()

    public function getStatus()
    {
        return $this->status;
    } // function getStatus()
} // class HttpException

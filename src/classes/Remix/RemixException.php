<?php

namespace Remix;

/**
 * Exception class of Remix
 *
 * @package  Remix\Base
 */
class RemixException extends \RuntimeException
{
    public function create(string $message, array $trace): self
    {
        $e = new static($message);
        $e->file = $trace['file'];
        $e->line = $trace['line'];
        return $e;
    }
}
// class RemixException

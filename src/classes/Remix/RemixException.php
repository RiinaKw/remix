<?php

namespace Remix;

/**
 * Exception base class of Remix.
 * It can only be used with subclasses.
 *
 * @package  Remix\Base
 */
abstract class RemixException extends \RuntimeException
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

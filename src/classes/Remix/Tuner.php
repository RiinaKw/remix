<?php

namespace Remix;

/**
 * Remix Tuner : Constant manager.
 */
class Tuner
{
    private $prop = null;

    public function __construct($value)
    {
        $this->prop = $value;
    }

    public function get()
    {
        return $this->prop;
    }

    public function isTrue(): bool
    {
        return ($this->prop === true);
    }

    public function __get(string $key): bool
    {
        return ($this->prop === $key);
    }
}
// class Tuner

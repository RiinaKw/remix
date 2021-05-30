<?php

namespace Remix;

abstract class Gear
{
    protected function __construct()
    {
        Delay::logBirth(static::class);
    }
    // function __construct()

    public function __destruct()
    {
        Delay::logDeath(static::class);
    }

    public static function factory($arg1 = null, $arg2 = null): self
    {
        if ($arg1 === null) {
            return new static();
        } elseif ($arg2 === null) {
            return new static($arg1);
        } else {
            return new static($arg1, $arg2);
        }
    }

    public function destroy(): void
    {
    }
}
// class Component

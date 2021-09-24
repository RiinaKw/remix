<?php

namespace Remix;

/**
 * Remix Gear : Base class of Remix
 *
 * @package  Remix\Base
 */
abstract class Gear
{
    /**
     * Let Delay know that an instance has been constructed.
     */
    public function __construct()
    {
        Delay::logBirth(static::class);
    }
    // function __construct()

    /**
     * Let Delay know that an instance has been destructed.
     */
    public function __destruct()
    {
        Delay::logDeath(static::class);
    }
}
// class Component

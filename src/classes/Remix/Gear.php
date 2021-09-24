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

    /**
     * Does it implementing the specified trait?
     * @param  string $name  Target trait
     * @return bool          Implemented or not
     */
    public function uses(string $name): bool
    {
        return isset(class_uses($this)[$name]);
    }

    /**
     * Does it implementing the trait Recordable?
     * @return bool  Implemented or not
     */
    public function recordable(): bool
    {
        return $this->uses('Remix\\Recordable');
    }
}
// class Component

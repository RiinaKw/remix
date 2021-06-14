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
    protected function __construct()
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
     * Factory method.
     *
     * @param  mixed $arg1  Arguments to the __construct.
     * @param  mixed $arg2  Arguments to the __construct.
     * @return self         Instance which was constructed.
     */
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

    public function uses($name)
    {
        return isset(class_uses($this)[$name]);
        //var_dump(class_uses($this), $name);
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

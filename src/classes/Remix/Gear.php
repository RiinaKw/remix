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
     * Option parameter for Delay
     * @var string
     */
    protected $log_param = '';

    /**
     * Let Delay know that an instance has been constructed.
     */
    public function __construct()
    {
        if ($this->log_param) {
            Delay::logBirth(static::class . ' [' . $this->log_param . ']');
        } else {
            Delay::logBirth(static::class);
        }
    }
    // function __construct()

    /**
     * Let Delay know that an instance has been destructed.
     */
    public function __destruct()
    {
        if ($this->log_param) {
            Delay::logDeath(static::class . ' [' . $this->log_param . ']');
        } else {
            Delay::logDeath(static::class);
        }
    }
}
// class Component

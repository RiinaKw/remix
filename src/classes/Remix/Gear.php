<?php

namespace Remix;

use Remix\Audio;

/**
 * Remix Gear : Base class of Remix
 *
 * @package  Remix\Base
 */
abstract class Gear
{
    /**
     * Audio instance.
     * @var Audio
     */
    protected $audio = null;

    /**
     * Option parameter for Delay.
     * @var string
     */
    private $log_param = '';

    /**
     * Let Delay know that an instance has been constructed.
     * @param string|null $log_param  If this parameter is set, it will be displayed in Delay
     * @return self
     */
    public function __construct(?string $log_param = null)
    {
        if ($log_param) {
            $this->log_param = $log_param;
            Delay::logBirth(static::class . ' [' . $this->log_param . ']');
        } else {
            Delay::logBirth(static::class);
        }
    }
    // function __construct()

    /**
     * Let Delay know that an instance has been destructed.
     * @return void
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
// class Gear

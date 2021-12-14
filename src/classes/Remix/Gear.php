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

    public static $instances = [];

    public static function getId($obj)
    {
        return spl_object_id($obj);
    }

    public static function addHash($obj)
    {
        $id = static::getId($obj);
        static::$instances[$id] = get_class($obj);
    }

    public static function removeHash($obj)
    {
        $id = static::getId($obj);
        $obj = null;
        unset(static::$instances[$id]);
    }

    public static function dumpHash()
    {
        var_dump(static::$instances);
    }

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

        static::addHash($this);
    }
    // function __construct()

    /**
     * Let Delay know that an instance has been destructed.
     * @return void
     */
    public function __destruct()
    {
        $this->audio = null;
        static::removeHash($this);

        if ($this->log_param) {
            Delay::logDeath(static::class . ' [' . $this->log_param . ']');
        } else {
            Delay::logDeath(static::class);
        }
    }
}
// class Gear

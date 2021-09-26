<?php

namespace Remix\Instruments;

use Remix\Gear;
use Remix\Instrument;

/**
 * Remix Equalizer : instance manager
 *
 * @package  Remix\Core
 */
class Equalizer extends Instrument
{
    /**
     * Array of singleton instances.
     * @var Gear[]
     * @todo Why not Hash?
     */
    private $container = [];

    /**
     * Get a singleton instance.
     *
     * @param  string $class  class name
     * @return Gear           object
     */
    public function singleton(string $class): Gear
    {
        if (! array_key_exists($class, $this->container)) {
            $this->container[$class] = $this->instance($class);
        }
        return $this->container[$class];
    }
    // function singleton()

    /**
     * Get a instance.
     *
     * @param  string $class  class name
     * @param  mixed  $args   Arguments of constructor
     * @return Gear           object
     */
    public function instance(string $class, $args = null): Gear
    {
        return new $class($args);
    }
    // function factory()

    public function __destruct()
    {
        if ($this->container) {
            foreach (array_keys($this->container) as $key) {
                unset($this->container[$key]);
            }
            $this->container = [];
        }

        parent::__destruct();
    }
    // function __destruct()
}
// class Equalizer

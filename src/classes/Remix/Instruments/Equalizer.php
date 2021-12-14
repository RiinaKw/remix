<?php

namespace Remix\Instruments;

// Remix core
use Remix\Gear;
use Remix\Instrument;
use Remix\Instruments\{
    DAW,
    Preset,
    Mixer,
    Amp,
    DJ
};
// Exceptions
use Remix\Exceptions\CoreException;

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

    /**
     * Destructor
     */
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

    /**
     * Getter
     * @param  string $key  Key of item
     * @return mixed        Any item
     */
    public function __get(string $key)
    {
        switch ($key) {
            case 'daw':
                return $this->singleton(DAW::class);

            case 'preset':
                return $this->singleton(Preset::class);

            case 'mixer':
                return $this->singleton(Mixer::class);

            case 'amp':
                return $this->singleton(Amp::class);

            case 'dj':
                return $this->singleton(DJ::class);

            default:
                throw new CoreException("Unknown key '{$key}'");
        }
    }
    // function __get()
}
// class Equalizer

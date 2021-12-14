<?php

namespace Remix\Instruments;

// Remix core
use Remix\Gear;
use Remix\Audio;
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
     * @var array<string, Gear>
     * @todo Why not Hash?
     */
    private $container = [];

    /**
     * Constructor.
     * @return self
     */
    public function __construct(Audio $audio)
    {
        parent::__construct();
        $this->audio = $audio;
    }

    /**
     * Destructor.
     * @return void
     */
    public function __destruct()
    {
        parent::__destruct();

        $this->audio = null;

        if ($this->container) {
            foreach (array_keys($this->container) as $key) {
                unset($this->container[$key]);
            }
            $this->container = [];
        }
    }
    // function __destruct()

    /**
     * Get a singleton instance.
     *
     * @param  string $class  class name
     * @param  mixed  $args   Arguments of constructor
     * @return Gear           object
     */
    public function singleton(string $class, $args = null): Gear
    {
        if (! array_key_exists($class, $this->container)) {
            $this->container[$class] = $this->instance($class, $args);
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
        $instance = new $class($args);
        $instance->audio = $this->audio;
        return $instance;
    }
    // function factory()

    /**
     * Getter.
     * @param  string $key  Key of item
     * @return mixed        Any item
     */
    public function __get(string $key)
    {
        switch ($key) {
            case 'daw':
                return $this->singleton(DAW::class, $this->audio);

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

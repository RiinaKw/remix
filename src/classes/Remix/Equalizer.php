<?php

namespace Remix;

/**
 * Remix Equalizer : instance manager
 *
 * @package  Remix\Core
 */
class Equalizer extends Gear
{
    private $container = [];

    public function singleton(string $class): Gear
    {
        if (! array_key_exists($class, $this->container)) {
            $this->container[$class] = $this->instance($class);
        }
        return $this->container[$class];
    }
    // function singleton()

    public function instance(string $class, $args = null): Gear
    {
        return $class::factory($args);
    }
    // function factory()

    public function destroy(): void
    {
        if ($this->container) {
            foreach ($this->container as $key => $item) {
                if (method_exists($item, 'destroy')) {
                    $item->destroy();
                }
                $item = null;
                unset($this->container[$key]);
            }
            $this->container = [];
        }
    }
    // function destroy()
}
// class Equalizer

<?php

namespace Remix;

/**
 * Remix Equalizer : instance manager
 */
class Equalizer extends Component
{
    private $container = [];

    public function singleton(string $class) : Component
    {
        if (! array_key_exists($class, $this->container)) {
            $this->container[$class] = $this->instance($class);
        }
        return $this->container[$class];
    } // function singleton()

    public function instance(string $class, $args = null) : Component
    {
        return $class::factory($args);
    } // function factory()

    public function destroy() : void
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
    } // function destroy()
} // class Equalizer

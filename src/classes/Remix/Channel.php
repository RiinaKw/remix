<?php

namespace Remix;

use Remix\Sampler;
use Remix\Studio;

/**
 * Remix Channel : web controller.
 *
 * @package  Remix\Web
 * @todo Write the details.
 */
abstract class Channel extends Gear
{
    /**
     * Execute a method of a subclass.
     * Also call "before()" and "after()" methods if they exist.
     *
     * @param  string         $method   Method name of subclass
     * @param  Sampler        $sampler  Input object
     * @return string|Studio
     *
     * @todo Wouldn't it be better for this method to be in Mixer?
     */
    public function play(string $method, Sampler $sampler)
    {
        if (! method_exists($this, $method)) {
            $class = get_class($this);
            throw new RemixException(
                "Channel '{$class}' does not contain the method '{$method}'"
            );
        }

        if (method_exists($this, 'before')) {
            $this->before($sampler);
        }

        $result = $this->$method($sampler);

        if (method_exists($this, 'after')) {
            $result = $this->after($sampler, $result);
        }
        return $result;
    }
}
// class Channel

<?php

namespace Remix;

/**
 * Remix Channel : web controller
 *
 * @package  Remix\Web
 * @todo Write the details.
 */
abstract class Channel extends \Remix\Gear
{
    public function play($args, Sampler $sampler)
    {
        if (! method_exists($this, $args)) {
            throw new \Remix\RemixException(
                'Channel "' . get_class($this)
                . '" does not contain the method "' . $args . '"'
            );
        }

        if (method_exists($this, 'before')) {
            $this->before($sampler);
        }

        $result = $this->$args($sampler);

        if (method_exists($this, 'after')) {
            $result = $this->after($sampler, $result);
        }
        return $result;
    }
}
// class Channel

<?php

namespace Remix;

use \Remix\Vinyl;

/**
 * Remix Turntable : presenter instance
 */
abstract class Turntable extends \Remix\Component
{
    protected $prop = [];

    public function __construct(Vinyl $vinyl)
    {
        parent::__construct();
        $this->prop = $vinyl->toArray();
    }

    public function __get($name)
    {
        return $this->prop[$name] ?? null;
    }

    public function __set($name, $value)
    {
        $this->prop[$name] = $value;
    }
}

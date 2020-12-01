<?php

namespace Remix;

use Remix\Vinyl;

/**
 * Remix Turntable : presenter instance
 */
abstract class Turntable extends Gear
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

    public function __set($name, $value): void
    {
        $this->prop[$name] = $value;
    }
}

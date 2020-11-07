<?php

namespace Remix;

class Vinyl extends \Remix\Component
{
    protected $prop = [];

    public function __get($name)
    {
        return $this->prop[$name] ?? null;
    }

    public function __set($name, $value)
    {
        $this->prop[$name] = $value;
    }

} // class Vinyl

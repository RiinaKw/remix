<?php

namespace Remix;

abstract class Component
{
    public function __construct($remix = null)
    {
        if ($remix) {
            throw new \Exception('param must be empty');
        }
    } // function __construct()
} // class Component

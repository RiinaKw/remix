<?php

namespace Remix;

abstract class Component
{
    protected function __construct($remix = null)
    {
        $method = str_replace(__CLASS__, static::class, __METHOD__);
        \Remix\App::getInstance()->logBirth($method);

        if ($remix) {
            throw new \Exception('param must be empty');
        }
    } // function __construct()

    public function __destruct()
    {
        $method = str_replace(__CLASS__, static::class, __METHOD__);
        \Remix\App::getInstance()->logDeath($method);
    }

    public static function factory($arg1 = null, $arg2 = null) : self
    {
        if ($arg1 === null) {
            return new static;
        } elseif ($arg2 === null) {
            return new static($arg1);
        } else {
            return new static($arg1, $arg2);
        }
    }
}

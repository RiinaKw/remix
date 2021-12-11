<?php

namespace Utility;

/**
 * Trait that realizes a singleton
 *
 * @package  Utility
 */
trait Singleton
{
    /**
     * Only one instance
     * @var self
     */
    protected static $instance = null;

    /**
     * Unable to create an instance from outside
     */
    private function __construct()
    {
        //
    }

    /**
     * Factory method
     * @return self  Only one instance
     */
    public static function factory(): self
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }
        return static::$instance;
    }
}

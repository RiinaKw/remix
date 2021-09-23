<?php

namespace Utility;

/**
 * Trait that realizes a singleton
 */
trait Singleton
{
    /**
     * Only one instance
     * @var self|null
     */
    protected static $instance = null;

    /**
     * Unable to create an instance from outside
     */
    protected function __construct()
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

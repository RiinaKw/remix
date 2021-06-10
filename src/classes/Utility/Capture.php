<?php

namespace Utility;

class Capture
{
    protected static function startCapture()
    {
        ob_start();
    }

    protected static function endCapture()
    {
        return ob_get_clean();
    }

    public static function capture($func)
    {
        static::startCapture();
        $func();
        return static::endCapture();
    }
}
// class Capture

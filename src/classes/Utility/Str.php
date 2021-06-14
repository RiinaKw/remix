<?php

namespace Utility;

/**
 * Utilities of string operations
 *
 * @package  Utility
 */
class Str
{
    public static function h($param)
    {
        return is_scalar($param) ? htmlspecialchars($param) : $param;
    }
}
// class Str

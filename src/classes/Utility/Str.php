<?php

namespace Utility;

/**
 * Utilities of string operations
 *
 * @package  Utility
 * @todo Write the details.
 */
class Str
{
    public static function h($param)
    {
        return is_scalar($param) ? htmlspecialchars($param) : $param;
    }
}
// class Str

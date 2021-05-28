<?php

namespace Remix\Utility;

class Str
{
    public static function h($param)
    {
        return is_scalar($param) ? htmlspecialchars($param) : $param;
    }
}
// class Str

<?php

namespace Remix\Utility\Performance;

class Memory
{
    const BYTE_TO_MEGA = 1024 * 1024;
    const UNIT = 'MiB';

    protected static function format($bytes)
    {
        return number_format($bytes / static::BYTE_TO_MEGA, 4) . ' ' . static::UNIT;
    }

    protected static function usage()
    {
        $bytes = memory_get_usage();
        return static::format($bytes);
    }

    protected static function usagePeak()
    {
        $bytes = memory_get_peak_usage();
        return static::format($bytes);
    }

    public static function get()
    {
        return sprintf('usage : %s / peak : %s', static::usage(), static::usagePeak());
    }
}

<?php

namespace Remix\Utility\Performance;

class Memory
{
    protected const BYTE_TO_MEGA = 1024 * 1024;
    protected const UNIT = 'MiB';

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
        $usage = static::usage();
        $peak = static::usagePeak();
        return "usage : {$usage} / peak : {$peak}";
    }
}
// class Memory

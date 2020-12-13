<?php

namespace Remix;

use Remix\Utility\Performance\Memory;
use Remix\Utility\Performance\Time;

class Delay
{
    private static $delay = null;

    private static $is_debug = false;
    private static $is_cli = false;

    private static $time = null;
    private static $log = [];

    private function __construct(bool $is_debug, bool $is_cli)
    {
        static::$is_debug = $is_debug;
        static::$is_cli = $is_cli;

        static::$time = new Time();
        static::$time->start();
    }

    public static function getInstance(bool $is_debug = false, $is_cli = false): self
    {
        if (! static::$delay) {
            static::$delay = new self($is_debug, $is_cli);
        }
        return static::$delay;
    }
    // function getInstance()

    public static function destroy(): void
    {
        static::$delay = null;
    }

    public static function log(string $type, string $str, string $flag = ''): void
    {
        $flag = $flag ? sprintf('%s ', $flag) : '';
        $log = [
            'type' => $type,
            'log' => $flag . $str,
        ];
        static::$log[] = $log;
        if (static::$is_debug && static::$is_cli) {
            static::stderr($log);
        }
    }

    public static function logBirth(string $str): void
    {
        static::log('TRACE', $str, '+');
    }
    // function logBirth()

    public static function logDeath(string $str): void
    {
        static::log('TRACE', $str, '-');
    }
    // function logDeath()

    public static function logMemory(): void
    {
        static::log('MEMORY', Memory::get());
    }
    // function logMemory()

    public static function logTime(): void
    {
        static::log('TIME', (string)static::$time->stop());
    }
    // function logTime()

    protected static function format(array $log): string
    {
        return sprintf("[%s] %s", $log['type'], $log['log']);
    }
    // function format()

    protected static function stderr(array $log): void
    {
        $color = '0;37';
        switch ($log['type']) {
            case 'BODY':
                $color = '1;30';
                break;
            case 'TRACE':
                $color = '1;34';
                break;
            case 'MEMORY':
                $color = '1;33';
                break;
            case 'TIME':
                $color = '0;35';
                break;
            case 'QUERY':
                $color = '0;36';
                break;
        }
        fprintf(STDERR, "\033[%sm %s\033[0m\n", $color, static::format($log));
    }
    // function stderr()

    public static function get(): string
    {
        $result = [];
        foreach (static::$log as $log) {
            $result[] = static::format($log);
        }
        return implode("<br />\n", $result);
    }
    // public function get()
}
// class Delay

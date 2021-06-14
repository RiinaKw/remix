<?php

namespace Remix;

use Utility\Performance\Memory;
use Utility\Performance\Time;

/**
 * Remix Delay : log manager
 *
 * @package  Remix\Core
 */
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
        if ($flag) {
            $flag .= ' ';
        }
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
        static::log('TRACE', $str, '+ birth');
    }
    // function logBirth()

    public static function logDeath(string $str): void
    {
        static::log('TRACE', $str, '- death');
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
        return "[{$log['type']}] {$log['log']}";
    }
    // function format()

    protected static function stderr(array $log): void
    {
        $text_color = '';
        $background_color = '';
        switch ($log['type']) {
            case 'BODY':
                $text_color = 'green';
                break;
            case 'TRACE':
                $text_color = 'light_blue';
                break;
            case 'MEMORY':
                $text_color = 'yellow';
                break;
            case 'TIME':
                $text_color = 'purple';
                break;
            case 'QUERY':
                $text_color = 'cyan';
                break;
        }
        Effector::lineError(static::format($log), $text_color, $background_color);
    }
    // function stderr()

    public static function get(): array
    {
        return static::$log;
    }
    // public function get()
}
// class Delay

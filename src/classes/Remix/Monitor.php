<?php

namespace Remix;

/**
 * Remix Monitor: debug tools
 *
 * @package  Remix\Core
 * @todo Write the details.
 */
class Monitor
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * parameter '$audio' is renamed dummy parameter from '$is_cli', no longer userd
     */
    public static function dump($var, Remix\Audio $audio = null): void
    {
        $trace = static::called(1);
        $is_cli = Audio::getInstance()->cli;

        if ($is_cli) {
            echo "\033[0;30m" . "\033[43m";
            var_dump($var);
            echo "{$trace['file']} ({$trace['line']})";
            echo "\033[0m", PHP_EOL;
        } else {
            echo '<section class="remix-monitor-dump" style="background-color: lightgray; padding: 1rem;">', PHP_EOL;
            echo "<strong>{$trace['file']} ({$trace['line']})</strong>", PHP_EOL;
            echo '<pre>', PHP_EOL;
            var_dump($var);
            echo '</pre>', PHP_EOL;
            echo '</section>', PHP_EOL;
        }
    }
    // function dump()

    public static function called(int $depth = 1): array
    {
        return debug_backtrace()[$depth];
    }

    public static function getSource(string $file, int $highlight, int $margin): array
    {
        $lines = file($file);
        $length = count($lines);

        $start = $highlight - $margin;
        if ($start <= 0) {
            $start = 1;
        }
        $end = $highlight + $margin;
        if ($end >= $length) {
            $end = $length - 1;
        }

        $target = [];
        for ($i = $start; $i <= $end; ++$i) {
            $line = str_replace("\n", '', $lines[$i - 1]);

            $arr = [
                'line' => $i,
                'source' => $line,
            ];
            if ($i == $highlight) {
                $arr['class'] = 'current';
            }
            $target[] = $arr;
        }

        return $target;
    }
    // function getSource()
}
// class Monitor

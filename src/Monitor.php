<?php

namespace Remix;

/**
 * Remix Monitor: debug tools
 */
class Monitor
{
    public static function dump($var) : void
    {
        $trace = debug_backtrace()[0];

        if (App::isCli()) {
            echo "\033[0;30m" . "\033[43m";
            var_dump($var);
            echo sprintf('%s (%d)', $trace['file'], $trace['line']), "\033[0m", PHP_EOL;
        } else {
            echo '<section class="remix-monitor-dump" style="background-color: lightgray; padding: 1rem;">', PHP_EOL;
            echo sprintf('<strong>%s (%d)</strong>', $trace['file'], $trace['line']), PHP_EOL;
            echo '<pre>';
            var_dump($var);
            echo '</pre>' . PHP_EOL;
            echo '</section>' . PHP_EOL;
        }
    } // function dump()

    public static function getSource(string $file, int $highlight, int $margin) : array
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
    } // function getSource()
} // class Monitor

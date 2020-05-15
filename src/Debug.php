<?php

namespace Remix;

class Debug
{
    public static function dump($var)
    {
        echo "<pre>";
        $trace = debug_backtrace()[0];
        var_dump($var);
        echo $trace['file'], "\nline: ", $trace['line'];
        echo "</pre>\n";
    } // function dump()

    public static function getSource(string $file, int $highlight, int $margin)
    {
        $lines = file($file);
        $length = count($lines);

        $start = $highlight - $margin;
        if ($start < 0) {
            $start = 0;
        }
        $end = $highlight + $margin;
        if ($end >= $length) {
            $end = $length - 1;
        }

        $target = [];
        for ($i = $start; $i <= $end; ++$i) {
            if ($i == $highlight - 1) {
                $target[] = $i+1 . ': <strong>' . $lines[$i] . '</strong>';
            } else {
                $target[] = $i+1 . ': ' . $lines[$i];
            }
        }

        return $target;
    } // function getSource()
} // class Debug

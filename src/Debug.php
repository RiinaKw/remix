<?php

namespace Remix;

class Debug
{
    public static function dump($var)
    {
        $trace = debug_backtrace()[0];

        echo "<pre>";
        var_dump($var);
        echo $trace['file'], "\nline: ", $trace['line'];
        echo "</pre>\n";
    } // function dump()

    public static function getSource(string $file, int $highlight, int $margin)
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
            $line = htmlspecialchars($line);
            if ($i == $highlight) {
                $target[] = '<li class="current">' . $i . ': ' . $line . '</li>';
            } else {
                $target[] = '<li>' . $i . ': ' . $line . '</li>';
            }
        }

        return $target;
    } // function getSource()
} // class Debug

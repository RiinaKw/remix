<?php

namespace Remix;

class Debug
{
    public static function dump($var) : void
    {
        $trace = debug_backtrace()[0];

        echo "<pre>";
        var_dump($var);
        echo $trace['file'], "\nline: ", $trace['line'];
        echo "</pre>\n";
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
            //$line = htmlspecialchars($line);

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
} // class Debug

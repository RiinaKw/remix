<?php

namespace Remix\Utility;

class Arr
{
    public static function flatten(array $arr): array
    {
        $v = [];
        array_walk_recursive(
            $arr,
            function ($e) use (&$v) {
                $v[] = $e;
            }
        );
        return $v;
    }

    public static function toXML(array $arr, $root = 'root'): string
    {
        $xml = '';
        foreach ($arr as $key => $value) {
            if (is_numeric($key)) {
                $key = 'item';
            }
            if (is_array($value)) {
                $xml .= static::toXML($value, $key);
            } else {
                $xml .= sprintf('<%s>%s</%s>', $key, $value, $key);
            }
        }
        return sprintf('<%s>%s</%s>', $root, $xml, $root);
    }
}
// class Arr

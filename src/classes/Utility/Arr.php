<?php

namespace Utility;

/**
 * Utilities of array operations
 *
 * @package  Utility
 * @todo Write the details.
 */
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
                $xml .= "<{$key}>{$value}</{$key}>";
            }
        }
        return "<{$root}>{$xml}</{$root}>";
    }
}
// class Arr

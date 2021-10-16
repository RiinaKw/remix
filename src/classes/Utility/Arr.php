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

    /**
     * Callback of map(), mapImplode()
     * @param  mixed $item  Elements contained in iterable
     * @return mixed|array<int, mixed>
     *             * mixed : An element contained in the new array
     *             * [mixed, int|string] : An array of two elements, the element and the key
     * @throws \Exception This is a prototype; not meant to be called directly.
     * @see Arr::map()
     * @see Arr::mapImplode()
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    private static function callbackMap($item)
    {
        throw new \Exception(__METHOD__ . ' is prototype of callback');
    }

    /**
     * Extend array_map to hash
     * @param  iterable $target  Source
     * @param  callable $cb      Callback applied to each element
     * @return array             Created array
     * @see Arr::callbackMap()
     */
    public static function map(iterable $target, callable $cb): array
    {
        $result = [];
        foreach ($target as $item) {
            $return = $cb($item);
            if (is_array($return)) {
                list($item, $key) = $return;
            } else {
                $item = $return;
                $key = null;
            }
            if ($key) {
                $result[$key] = $item;
            } else {
                $result[] = $item;
            }
        }
        return $result;
    }

    /**
     * Create an array and implode
     * @param  iterable $target     Source
     * @param  string   $separator  Separator to implode
     * @param  callable $cb         Callback applied to each element
     * @return string               Imploded string
     * @see Arr::callbackMap()
     */
    public static function mapImplode(iterable $target, string $separator, callable $cb): string
    {
        $array = static::map($target, $cb);
        return implode($separator, $array);
    }
}
// class Arr

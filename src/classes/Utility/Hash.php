<?php

namespace Utility;

/**
 * [Hash description]
 */
class Hash
{
    protected $prop = [];

    public function __construct($array = [])
    {
        $this->prop = $array;
    }

    public function ref(&$arr): void
    {
        $this->prop = &$arr;
    }
    // function ref()

    protected function callback($name, $item_func, $final_func = null)
    {
        $arr = explode('.', $name);
        $last = array_pop($arr);
        $target = &$this->prop;
        foreach ($arr as $key) {
            if (! isset($target[$key])) {
                $result = $item_func($target[$key]);
                if ($result === false) {
                    return null;
                }
            }
            $target = &$target[$key];
        }
        if ($final_func) {
            return $final_func($target, $last);
        }
        return null;
    }
    // function callback()

    public function get(string $name = '')
    {
        if ($name === '') {
            return $this->prop;
        }
        if (strpos($name, '.') !== false) {
            $cb_item = function (&$target) {
                return isset($target);
            };
            $cb_final = function ($target, $last) {
                return $target[$last] ?? null;
            };
            $result = $this->callback($name, $cb_item, $cb_final);
            return $result;
        } else {
            return $this->isset($name) ? $this->prop[$name] : null;
        }
    }
    // function get()

    public function set(string $name, $value): void
    {
        if (strpos($name, '.') !== false) {
            $cb_item = function (&$target) {
                if (! isset($target)) {
                    $target = null;
                }
            };
            $cb_final = function (&$target, $last) use ($value) {
                $target[$last] = $value;
            };
            $this->callback($name, $cb_item, $cb_final);
        } else {
            $this->prop[$name] = $value;
        }
    }
    // function set()

    public function isset(string $name): bool
    {
        if (strpos($name, '.') !== false) {
            $cb_item = function (&$target) {
                return isset($target);
            };
            $cb_final = function (&$target, $last) {
                return array_key_exists($last, $target);
            };
            return $this->callback($name, $cb_item, $cb_final) ?: false;
        } else {
            return isset($this->prop[$name]);
        }
    }
    // function isset()

    public function delete(string $name): void
    {
        if (strpos($name, '.') !== false) {
            $cb_item = function (&$target) {
                return isset($target);
            };
            $cb_final = function (&$target, $last) {
                unset($target[$last]);
                return true;
            };
            $this->callback($name, $cb_item, $cb_final);
        } else {
            unset($this->prop[$name]);
        }
    }
    // function delete()

    public function push(string $name, $value, ?string $key = null): void
    {
        if ($this->isset($name) && !is_array($this->get($name))) {
            throw Error();
        }
        $arr = $this->get($name) ?: [];
        if ($key === null) {
            $arr[] = $value;
        } else {
            $arr[$key] = $value;
        }
        $this->set($name, $arr);
    }

    public function pushHash(string $name, $value)
    {
        $arr = $this->get($name) ?: [];
        foreach ($value as $key => $item) {
            $arr[$key] = $item;
        }
        $this->set($name, $arr);
    }

    public function __get(string $name)
    {
        return $this->get($name);
    }
    // function __get()

    public function __set(string $name, $value)
    {
        return $this->set($name, $value);
    }
    // function __set()
}
// class Hash

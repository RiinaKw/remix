<?php

namespace Utility\Hash;

/**
 * Hash with editable
 *
 * @package  Utility\Hash
 */
trait Editable
{
    /**
     * @property array $source
     */

     /**
      * Set Hash value
      * @param string $key    key of item
      * @param mixed  $value  value of item
      */
    public function set(string $key, $value)
    {
        $parent =& $this->parent($key, true);
        $last_key = $this->lastKey($key);
        $parent[$last_key] = $value;
    }

    /**
     * Delete item
     * @param  string $key  target key
     */
    public function delete(string $key): void
    {
        $parent =& $this->parent($key);
        $last_key = $this->lastKey($key);
        unset($parent[$last_key]);
    }

    /**
     * magic method
     * @see self::set()
     */
    public function __set(string $key, $value)
    {
        $this->set($key, $value);
    }

    /**
     * magic method
     * @see self::delete()
     */
    public function __unset(string $key)
    {
        $this->delete($key);
    }

    /**
     * Truncate Hash
     */
    public function truncate(): void
    {
        $this->source = [];
    }

    public function push(string $name, $value, ?string $key = null): void
    {
        if ($key === null) {
            $parent =& $this->parent($name);
            $parent[$name] = [];
            $parent[$name][] = $value;
        } else {
            $new_key = $name . '.' . $key;
            $this->set($new_key, $value);
        }
    }

    public function pushHash(string $name, array $value)
    {
        foreach ($value as $key => $item) {
            $this->set($name . '.' . $key, $item);
        }
    }
}

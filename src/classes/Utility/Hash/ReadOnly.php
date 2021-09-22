<?php

namespace Utility\Hash;

/**
 * Hash with read-only
 *
 * @package  Utility\Hash
 */
class ReadOnly
{
    /**
     * properties
     * @var array<string, mixed>
     */
    protected $source = [];

    public function __construct(array $array = [])
    {
        $this->source = $array;
    }

    /**
     * Source array as a reference
     * @param array $array  reference of source
     */
    public function ref(array &$array): void
    {
        $this->source =& $array;
    }

    /**
     * Search
     * @param  string  $key                  key of hash item
     * @param  boolean $create               true to create a key that does not exist
     * @return array<string, mixed>|null     parent array of target item
     */
    protected function &parent(string $key = '', bool $create = false): ?array
    {
        $null = null;
        if (strpos($key, '.') === false) {
            return $this->source;
        } else {
            $keys = $this->parentKeys($key);
            $parent =& $this->source;
            foreach ($keys as $k) {
                if (! isset($parent[$k])) {
                    if ($create) {
                        $parent[$k] = [];
                    } else {
                        return $null;
                    }
                }
                $parent =& $parent[$k];
            }
            if (is_array($parent)) {
                return $parent;
            } elseif ($parent !== null && $create) {
                throw new \Exception('cannot override');
            }
            return $null;
        }
    }

    protected function key2arr(string $key): array
    {
        return explode('.', $key);
    }

    protected function parentKeys(string $key): array
    {
        $keys = $this->key2arr($key);
        array_pop($keys);
        return $keys;
    }

    protected function lastKey(string $key): string
    {
        $keys = $this->key2arr($key);
        return array_pop($keys);
    }

    /**
     * Is Hash empty?
     * @return bool  true if empty
     */
    public function isEmpty(): bool
    {
        return count($this->source) === 0;
    }

    /**
     * Is key contained in Hash?
     * @param  string $key  target key
     * @return bool         true if be contained
     */
    public function isSet(string $key): bool
    {
        $parent =& $this->parent($key);
        $last_key = $this->lastKey($key);
        return isset($parent[$last_key]);
    }

    /**
     * Get Hash value
     * @param  string $key  key of item
     * @return mixed        value
     */
    public function get(string $key = '')
    {
        if ($key === '') {
            return $this->source;
        }
        $parent =& $this->parent($key);
        $last_key = $this->lastKey($key);
        return $parent[$last_key] ?? null;
    }

    /**
     * magic method
     * @see self::get()
     */
    public function __get(string $key)
    {
        return $this->get($key);
    }

    /**
     * magic method
     * @see self::isSet()
     */
    public function __isset(string $key): bool
    {
        return $this->isSet($key);
    }
}

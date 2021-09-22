<?php

namespace Utility;

/**
 * Utilities of hash operations
 *
 * @package  Utility
 * @todo Write the details.
 */
class Hash
{
    private $source = [];

    public function __construct(array $array = [])
    {
        $this->source = $array;
    }

    public function ref(array &$array): void
    {
        $this->source =& $array;
    }

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

    public function isset(string $key): bool
    {
        $parent =& $this->parent($key);
        $last_key = $this->lastKey($key);
        return isset($parent[$last_key]);
    }

    public function get(string $key = '')
    {
        if ($key === '') {
            return $this->source;
        }
        $parent =& $this->parent($key);
        $last_key = $this->lastKey($key);
        return $parent[$last_key] ?? null;
    }

    public function set(string $key, $value)
    {
        $parent =& $this->parent($key, true);
        $last_key = $this->lastKey($key);
        $parent[$last_key] = $value;
    }

    public function delete(string $key)
    {
        $parent =& $this->parent($key);
        $last_key = $this->lastKey($key);
        unset($parent[$last_key]);
    }

    public function __get(string $key)
    {
        return $this->get($key);
    }

    public function __set(string $key, $value)
    {
        $this->set($key, $value);
    }

    public function __isset(string $key): bool
    {
        return $this->isset($key);
    }

    public function __unset(string $key)
    {
        $this->delete($key);
    }

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

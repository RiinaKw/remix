<?php

namespace Utility\Hash;

use Exception;

/**
 * Hash with read-only
 *
 * @package  Utility\Hash
 */
class ReadOnlyHash
{
    /**
     * properties
     * @var array<string, mixed>
     */
    protected $source = [];

    /**
     * Constructor
     * @param array<string, mixed> $array  source array
     */
    public function __construct(array $array = [])
    {
        $this->source = $array;
    }

    /**
     * Is Hash editable?
     * @return bool  editable or not
     */
    protected function isEditable(): bool
    {
        return isset(
            class_uses($this)[Editable::class]
        );
    }

    /**
     * Source array as a reference
     * @param array<string, mixed> $array  reference of source
     */
    public function ref(array &$array): void
    {
        $this->source =& $array;
    }

    /**
     * Search parent
     * @param  string  $key                  key of item
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
                    if ($this->isEditable() && $create) {
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
                throw new Exception('cannot override');
            }
            return $null;
        }
    }

    /**
     * Convert a dot-connected character string to an array
     * @param  string $key         key
     * @return array<int, string>  exploded key
     */
    protected function key2arr(string $key): array
    {
        return explode('.', $key);
    }

    /**
     * Take out the key pointing to the parent
     * @param  string $key         key
     * @return array<int, string>  array of keys pointing to the parent
     */
    protected function parentKeys(string $key): array
    {
        $keys = $this->key2arr($key);
        array_pop($keys);
        return $keys;
    }

    /**
     * Get the key directly under the parent
     * @param  string $key  key
     * @return string       key of last child
     */
    protected function lastKey(string $key): string
    {
        $keys = $this->key2arr($key);
        return array_pop($keys);
    }

    /**
     * Is Hash empty?
     * @return bool  empty or not
     */
    public function isEmpty(): bool
    {
        return count($this->source) === 0;
    }

    /**
     * Is key contained in Hash?
     * @param  string $key  target key
     * @return bool         contained or not
     */
    public function isSet(string $key): bool
    {
        $parent =& $this->parent($key);
        $last_key = $this->lastKey($key);
        return isset($parent[$last_key]);
    }

    /**
     * Get Hash value
     * @param  string $key     key of item
     * @param  mixed $default  default value if empty
     * @return mixed           value
     */
    public function get(string $key = '', $default = null)
    {
        if ($key === '') {
            return $this->source;
        }
        $parent =& $this->parent($key);
        $last_key = $this->lastKey($key);
        return (isset($parent[$last_key]) && $parent[$last_key] !== '') ? $parent[$last_key] : $default;
    }

    /**
     * Get item object
     * @param  string $key  key of item
     * @return Item         item object
     */
    public function item(string $key): Item
    {
        return new Item($this, $key);
    }

    /**
     * magic method
     * @see ReadOnlyHash::get()
     */
    public function __get(string $key)
    {
        return $this->get($key);
    }

    /**
     * magic method
     * @see ReadOnlyHash::isSet()
     */
    public function __isset(string $key): bool
    {
        return $this->isSet($key);
    }
}

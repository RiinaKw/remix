<?php

namespace Utility\Hash;

use Exception;

/**
 * Hash item
 *
 * @package  Utility\Hash
 */
class Item
{
    /**
     * Parent hash object
     * @var ReadOnlyHash
     */
    private $parent = null;

    /**
     * Target hash key
     * @var string
     */
    private $key = '';

    public function __construct(ReadOnlyHash $parent, string $key)
    {
        $this->parent = $parent;
        $this->key = $key;
    }

    /**
     * Is Hash editable?
     * @return bool  editable or not
     */
    protected function isEditable(): bool
    {
        return isset(
            class_uses($this->parent)[Editable::class]
        );
    }

    protected function requireEditable(): void
    {
        if (! $this->isEditable()) {
            throw new Exception("Hash item '{$this->key}' is not editable");
        }
    }

    public function get($default = null)
    {
        return $this->parent->get($this->key, $default);
    }

    public function set($value)
    {
        $this->requireEditable();
        $this->parent->set($this->key, $value);
    }

    public function delete()
    {
        $this->requireEditable();
        return $this->parent->delete($this->key);
    }
}

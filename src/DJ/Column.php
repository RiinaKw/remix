<?php

namespace Remix\DJ;

use Remix\Gear;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class Column extends Gear
{
    protected $props = [];

    public function __construct(string $name, $type, $param = '')
    {
        $this->props['name'] = $name;
        $this->props['type'] = $type;
        $this->props['param'] = $param;
        $this->props['nullable'] = false;
        $this->props['unsigned'] = false;
        //$this->props['default'] = ''; // unset is default
        $this->props['additional'] = [];
    }

    public function __get(string $key)
    {
        switch ($key) {
            case 'name':
            case 'index':
                return $this->props[$key];
        }
        return null;
    }

    public function autoIncrement(): self
    {
        $this->props['additional'][] = 'AUTO_INCREMENT';
        return $this;
    }

    public function unsigned(): self
    {
        $this->props['unsigned'] = true;
        return $this;
    }

    public function nullable(): self
    {
        $this->props['nullable'] = true;
        return $this;
    }

    public function default($value): self
    {
        $this->props['default'] = $value;
        return $this;
    }

    public function currentTimestamp(): self
    {
        $this->props['default'] = 'current_timestamp()';
        return $this;
    }

    public function pk(): self
    {
        $this->props['index'] = 'pk';
        return $this;
    }

    public function unique(): self
    {
        $this->props['index'] = 'unique';
        return $this;
    }

    public function index(): self
    {
        $this->props['index'] = 'index';
        return $this;
    }

    private function definitionType(): string
    {
        $type = '';
        switch ($this->props['type']) {
            case 'INT':
                if ($this->props['param']) {
                    $type = sprintf('INT(%d)', $this->props['param']);
                } else {
                    $type = 'INT';
                }
                if ($this->props['unsigned']) {
                    $type .= ' UNSIGNED';
                }
                break;

            case 'VARCHAR':
                $type = sprintf('VARCHAR(%d)', $this->props['param']);
                break;

            case 'TEXT':
            case 'DATETIME':
            case 'TIMESTAMP':
                $type = $this->props['type'];
                break;
        }
        return sprintf('`%s` %s', $this->props['name'], $type);
    }

    private function definitionDefaultValue(): string
    {
        if (array_key_exists('default', $this->props)) {
            $default = $this->props['default'] ?? null;
            $formatted = '';
            if ($default === null) {
                $formatted = 'NULL';
            } elseif ($default === 'current_timestamp()') {
                $formatted = $default;
            } elseif (is_string($default)) {
                $formatted = sprintf("'%s'", $default);
            } elseif (is_int($default)) {
                $formatted = $default;
            }
            return sprintf(' DEFAULT %s', $formatted);
        }
        return '';
    }

    public function __toString()
    {
        $text = $this->definitionType();
        $text .= ' ' . ($this->props['nullable'] ? 'NULL' : 'NOT NULL');
        $text .= $this->definitionDefaultValue();
        $text .= $this->props['additional'] ? (' ' . implode(' ', $this->props['additional'])) : '';
        return $text;
    }
}
// class Column

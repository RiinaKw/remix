<?php

namespace Remix\DJ;

use Remix\Gear;
use Remix\Exceptions\DJException;

/**
 * Remix DJ Column : column definition of DB table
 *
 * @package  Remix\DB
 * @todo Write the details.
 */
class Column extends Gear
{
    protected $props = [];

    protected function __construct(string $name, array $params = [])
    {
        $this->props['name'] = $name;
        $this->props['type'] = strtoupper($params['type']);
        $this->props['length'] = $params['length'] ?? false;
        $this->props['nullable'] = false;
        $this->props['unsigned'] = false;
        //$this->props['default'] = ''; // unset is default
        $this->props['additional'] = [];
        $this->props['index'] = '';
    }

    public function __get(string $key)
    {
        switch ($key) {
            case 'name':
            case 'index':
                return $this->props[$key] ?? null;
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

    private function definitionType(): string
    {
        $type = '';
        switch ($this->props['type']) {
            case 'INT':
                if ($this->props['length']) {
                    $type = "INT({$this->props['length']})";
                } else {
                    $type = 'INT';
                }
                if ($this->props['unsigned']) {
                    $type .= ' UNSIGNED';
                }
                break;

            case 'VARCHAR':
                $type = "VARCHAR({$this->props['length']})";
                break;

            case 'TEXT':
            case 'DATETIME':
            case 'TIMESTAMP':
                $type = $this->props['type'];
                break;
        }
        return "`{$this->props['name']}` {$type}";
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
                $formatted = "'" . $default . "'";
            } elseif (is_int($default)) {
                $formatted = $default;
            }
            return ' DEFAULT ' . $formatted;
        }
        return '';
    }

    public function __call(string $name, array $args): self
    {
        switch ($name) {
            case 'pk':
            case 'uq':
            case 'idx':
                $this->props['index'] = $name;
                break;
        }
        return $this;
    }

    public function __toString()
    {
        $text = $this->definitionType();
        $text .= ' ' . ($this->props['nullable'] ? 'NULL' : 'NOT NULL');
        $text .= $this->definitionDefaultValue();
        $text .= $this->props['additional'] ? (' ' . implode(' ', $this->props['additional'])) : '';
        $text .= $this->props['index'] === 'pk' ? ' PRIMARY KEY' : '';
        return $text;
    }
}
// class Column

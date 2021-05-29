<?php

namespace Remix\DJ;

use Remix\Gear;
use Remix\Exceptions\DJException;

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

    public function __call($name, $args = null): self
    {
        switch ($name) {
            case 'pk':
            case 'unique':
            case 'index':
                $this->props['index'] = $name;
                return $this;
        }
        throw new DJException('unknown method "' . $name . '"');
        return $this;
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
                    $type = sprintf('INT(%d)', $this->props['length']);
                } else {
                    $type = 'INT';
                }
                if ($this->props['unsigned']) {
                    $type .= ' UNSIGNED';
                }
                break;

            case 'VARCHAR':
                $type = sprintf('VARCHAR(%d)', $this->props['length']);
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
        $text .= $this->props['index'] === 'pk' ? ' PRIMARY KEY' : '';
        return $text;
    }
}
// class Column

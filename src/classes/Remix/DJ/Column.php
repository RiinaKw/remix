<?php

namespace Remix\DJ;

use Remix\Gear;
use Remix\Exceptions\DJException;

/**
 * Remix DJ Column : column definition of DB table
 *
 * @package  Remix\DB\Table
 * @todo Write the details.
 */
abstract class Column extends Gear
{
    protected $name = '';
    protected $type = '';
    protected $props = [];

    public function __construct(string $name, string $type, array $params = [])
    {
        $this->name = $name;
        $this->type = strtoupper($type);
        $this->props['length'] = $params['length'] ?? false;
        $this->props['nullable'] = $params['nullable'] ?? false;
        $this->props['unsigned'] = $params['unsigned'] ?? false;
        if (isset($params['default'])) {
            $this->props['default'] = $params['default'];
        }
        $this->props['additional'] = [];
        $this->props['index'] = $params['index'] ?? '';
    }

    public static function constructFromDef(array $def = []): self
    {
        $type = strtolower($def['Type']);

        preg_match('/^(?<type>\w+)(\((?<length>\d+)\))?/', $type, $matches);
        $name = $def['Field'];
        $type = $matches['type'];
        $length = $matches['length'] ?? 0;

        switch ($type) {
            case 'int':
                $column = Columns\IntCol::fromDef($name, $length, $def);
                break;

            case 'varchar':
                $column = Columns\VarcharCol::fromDef($name, $length, $def);
                break;

            case 'text':
                $column = Columns\TextCol::fromDef($name, $def);
                break;

            case 'datetime':
                $column = Columns\DatetimeCol::fromDef($name, $def);
                break;

            case 'timestamp':
                $column = Columns\TimestampCol::fromDef($name, $def);
                break;

            default:
                $message = 'unknown method ' . $type;
                throw new DJException($message);
        }
        if ($def['Null'] === 'YES') {
            $column->nullable();
        }
        if ($def['Default'] !== null) {
            $column->default($def['Default']);
        }
        if ($def['Comment'] !== null) {
            $column->comment($def['Comment']);
        }
        return $column;
    }

    public function __get(string $key)
    {
        switch ($key) {
            default:
                return $this->props[$key] ?? null;
            case 'name':
                return $this->name;
            case 'type':
                return $this->type;
        }
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

    private function definitionType(): string
    {
        switch ($this->type) {
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
                $type = $this->type;
                break;
        }
        return "`{$this->name}` {$type}";
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

    public function pk(): self
    {
        $this->props['index'] = 'pk';
        return $this;
    }

    public function uq(): self
    {
        $this->props['index'] = 'uq';
        return $this;
    }

    public function idx(): self
    {
        $this->props['index'] = 'idx';
        return $this;
    }

    public function comment(string $comment): self
    {
        $this->props['comment'] = $comment;
        return $this;
    }

    public function __toString()
    {
        $text = $this->definitionType();
        $text .= ' ' . ($this->props['nullable'] ? 'NULL' : 'NOT NULL');
        $text .= $this->definitionDefaultValue();
        $text .= $this->props['additional'] ? (' ' . implode(' ', $this->props['additional'])) : '';
        $text .= $this->props['index'] === 'pk' ? ' PRIMARY KEY' : '';
        if (isset($this->props['comment'])) {
            $text .= " COMMENT '{$this->props['comment']}'";
        }
        return $text;
    }
}
// class Column

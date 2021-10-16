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
    protected $table = '';
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

    public static function __callStatic($type, $args): self
    {
        $name = $args[0];
        switch ($type) {
            case 'int':
                return new Columns\IntCol($name, ($args[1] ?? false));
                break;

            case 'varchar':
                return new Columns\VarcharCol($name, ($args[1] ?? false));
                break;

            case 'text':
                return new Columns\TextCol($name);
                break;

            case 'datetime':
                return new Columns\DatetimeCol($name);
                break;

            case 'timestamp':
                return new Columns\TimestampCol($name);
                break;

            default:
                $message = "unknown column type '{$type}'";
                throw new DJException($message);
        }
    }

    public function appendTo(Table $table): self
    {
        $this->table = $table->name;
        $table->append($this);
        return $this;
    }

    public function modify(Table $table): self
    {
        $this->table = $table->name;
        $table->addColumn($this);
        return $this;
    }

    public static function constructFromDef(array $def): self
    {
        $type = strtolower($def['Type']);

        preg_match('/^(?<type>\w+)(\((?<length>\d+)\))?/', $type, $matches);
        $name = $def['Field'];
        $type = $matches['type'];
        $length = $matches['length'] ?? 0;

        $types = [
            'int' => [
                'class' => Columns\IntCol::class,
                'ignore_length' => false,
            ],
            'varchar' => [
                'class' => Columns\VarcharCol::class,
                'ignore_length' => false,
            ],
            'text' => [
                'class' => Columns\TextCol::class,
                'ignore_length' => true,
            ],
            'datetime' => [
                'class' => Columns\DatetimeCol::class,
                'ignore_length' => true,
            ],
            'timestamp' => [
                'class' => Columns\TimestampCol::class,
                'ignore_length' => true,
            ],
        ];
        if (! isset($types[$type])) {
            $message = 'unknown method ' . $type;
            throw new DJException($message);
        }
        $typedef = $types[$type];
        if ($typedef['ignore_length']) {
            $column = $typedef['class']::fromDef($name, $def);
        } else {
            $column = $typedef['class']::fromDef($name, $length, $def);
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

        $indexes = [
            'PRI' => function ($column) {
                $column->pk();
            },
            'UNI' => function ($column) {
                $column->uq();
            },
            'MUL' => function ($column) {
                $column->idx();
            },
        ];
        if (isset($indexes[$def['Key']])) {
            $indexes[$def['Key']]($column);
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

    public function __call(string $key, array $arg): self
    {
        switch ($key) {
            case 'pk':
            case 'uq':
            case 'idx':
                $this->props['index'] = $key;
                break;

            case 'nullable':
                $this->props[$key] = true;
                break;

            case 'default':
            case 'comment':
            case 'after':
                $this->props[$key] = $arg[0];
                break;

            default:
                throw new DJException("unknown method '{$key}'");
        }
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

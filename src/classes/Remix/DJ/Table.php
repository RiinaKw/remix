<?php

namespace Remix\DJ;

use Remix\Gear;
use Remix\Instruments\DJ;
use Remix\DJ\MC;
use Remix\DJ\BPM;
use Remix\DJ\BPM\Select;
use Remix\Exceptions\DJException;

/**
 * Remix DJ Table : DB tables definition
 *
 * @package  Remix\DB\Table
 * @todo Write the details.
 */
class Table extends Gear
{
    protected $name;
    protected $comment = '';
    protected $columns = [];
    protected $columns_add = [];

    protected $indexes_cache = null;

    public function __construct(string $name)
    {
        if (preg_match('/\W/', $name)) {
            $message = "Illegal table name '{$name}'";
            throw new DJException($message);
        }
        parent::__construct();
        $this->name = $name;
    }
    // function __construct()

    public function __get(string $key)
    {
        switch ($key) {
            case 'name':
            case 'comment':
            case 'columns':
                return $this->$key;

            default:
                $message = 'Unknown property "' . $key . '"';
                throw new DJException($message);
        }
    }
    // function __get()

    public function comment(string $comment)
    {
        $this->comment = $comment;
    }

    public function append(Column $column)
    {
        $this->columns[$column->name] = $column;
    }

    public function addColumn(Column $column)
    {
        $this->columns_add[$column->name] = $column;
    }

    public function select(): BPM
    {
        return new Select($this->name);
    }
    // function select()

    public function create(callable $cb): bool
    {
        if (MC::tableExists($this->name)) {
            throw new DJException("Table '($this->name)' is already exists");
        }

        $cb($this);
        if (count($this->columns) < 1) {
            throw new DJException("Table '{$this->name}' must contains any column");
        }
        $columns_string = [];
        foreach ($this->columns as $column) {
            $columns_string[] = (string)$column;
        }
        $columns = implode(', ', $columns_string);
        $sql = "CREATE TABLE `{$this->name}` ({$columns});";

        try {
            if (DJ::play($sql)) {
                foreach ($this->columns as $column) {
                    $this->createIndex($column);
                }
                return true;
            } else {
                throw new DJException("Cannot create table '{$this->name}'");
            }
        } catch (\Exception $e) {
            throw new DJException($e->getMessage());
        }
        return false;
    }
    // function create()

    public function modify(callable $cb): bool
    {
        if (! MC::tableExists($this->name)) {
            throw new DJException("Table '{$this->name}' does not exists");
        }
        $cb($this);

        if ($this->columns_add) {
            $columns_string = [];
            foreach ($this->columns_add as $column) {
                $sql = 'ADD COLUMN ' . (string)$column;
                if ($column->after) {
                    $sql .= " AFTER `{$column->after}`";
                }
                $columns_string[] = $sql;
            }
            $columns_sql = implode(', ', $columns_string);
            $sql = "ALTER TABLE `{$this->name}` {$columns_sql};";

            try {
                if (DJ::play($sql)) {
                    foreach ($this->columns_add as $column) {
                        $this->createIndex($column);
                    }
                    return true;
                } else {
                    throw new DJException("Cannot modify table '{$this->name}'");
                }
            } catch (\Exception $e) {
                throw new DJException($e->getMessage());
            }
        }

        return false;
    }

    public function createIndex(Column $column): void
    {
        if (! MC::tableExists($this->name)) {
            throw new DJException("Table '{$this->name}' does not exists");
        }

        switch ($column->index) {
            case '':
            case 'pk':
                // ignore
                return;

            case 'idx':
                $index_type = 'INDEX';
                $prefix = 'idx';
                break;

            case 'uq':
                $index_type = 'UNIQUE INDEX';
                $prefix = 'uq';
                break;

            default:
                $message = 'Unknown index type "' . $column->index . '"';
                throw new DJException($message);
        }
        $index_name = $prefix . '__' . $this->name . '__' . $column->name;

        $sql = "CREATE {$index_type} `{$index_name}` ON `{$this->name}`(`{$column->name}`);";
        $results = DJ::play($sql);
        if (! $results) {
            throw new DJException("Cannot create index '{$index_name}' for table '{$this->name}'");
        }
    }
    // function createIndex()
}
// class Table

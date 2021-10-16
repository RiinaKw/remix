<?php

namespace Remix\DJ\Table;

use Remix\Gear;
use Remix\Instruments\DJ;
use Remix\DJ\MC;
use Remix\DJ\Table;
use Remix\DJ\Column;
use Remix\Exceptions\DJException;

/**
 * Remix DJ Table Operate : SQL manager of Table
 *
 * @package  Remix\DB\Table
 * @todo Write the details.
 */
class Operate extends Gear
{
    protected $table = null;

    public function __construct(Table $table)
    {
        $this->table = $table;
    }

    public function truncate(): bool
    {
        $name = $this->table->name;
        if (MC::tableExists($name)) {
            return DJ::play("TRUNCATE TABLE `{$name}`;") !== false;
        } else {
            $message = "Table '{$name}' is not exists";
            throw new DJException($message);
        }
    }
    // function truncate()

    public function create(callable $cb): bool
    {
        $name = $this->table->name;
        if (MC::tableExists($name)) {
            throw new DJException("Table {$name} is already exists");
        }

        $cb($this->table);
        $columns = $this->table->columns;
        if (count($columns) < 1) {
            throw new DJException("Table '{$name}' must contains any column");
        }

        $columns_string = [];
        foreach ($columns as $column) {
            $columns_string[] = (string)$column;
        }
        $columns_sql = implode(', ', $columns_string);
        $sql = "CREATE TABLE `{$name}` ({$columns_sql})";
        if ($this->table->comment) {
            $sql .= " COMMENT='{$this->table->comment}'";
        }
        $sql .= ';';

        try {
            if (DJ::play($sql)) {
                foreach ($columns as $column) {
                    $this->createIndex($column);
                }
                return true;
            } else {
                throw new DJException("Cannot create table '{$name}'");
            }
        } catch (\Exception $e) {
            throw new DJException($e->getMessage());
        }
        return false;
    }
    // function create()

    public function modify(array $columns): bool
    {
        $name = $this->table->name;
        if (! MC::tableExists($name)) {
            throw new DJException("Table '{$name}' does not exists");
        }

        $add_columns = [];
        foreach ($columns as $key => $column) {
            if (is_numeric($key)) {
                // add
                $add_columns[] = $column;
            } else {
                // modify
            }
        }
        if ($add_columns) {
            $columns_string = [];
            foreach ($add_columns as $column) {
                $sql = 'ADD COLUMN ' . (string)$column;
                if ($column->after) {
                    $sql .= " AFTER `{$column->after}`";
                }
                $columns_string[] = $sql;
            }
            $columns_sql = implode(', ', $columns_string);
            $sql = "ALTER TABLE `{$name}` {$columns_sql};";

            try {
                if (DJ::play($sql)) {
                    foreach ($add_columns as $column) {
                        $this->createIndex($column);
                    }
                    return true;
                } else {
                    throw new DJException("Cannot modify table '{$name}'");
                }
            } catch (\Exception $e) {
                throw new DJException($e->getMessage());
            }
        }
        return false;
    }

    public function createIndex(Column $column): void
    {
        $name = $this->table->name;
        if (! MC::tableExists($name)) {
            throw new DJException("Table '{$name}' does not exists");
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
        $index_name = $prefix . '__' . $name . '__' . $column->name;

        $sql = "CREATE {$index_type} `{$index_name}` ON `{$name}`(`{$column->name}`);";
        $results = DJ::play($sql);
        if (! $results) {
            throw new DJException("Cannot create index '{$index_name}' for table '{$name}'");
        }
    }
    // function createIndex()
}

<?php

namespace Remix\DJ\Table;

use Remix\Gear;
use Remix\Instruments\DJ;
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

    public function exists(): bool
    {
        $name = $this->table->name;
        $result = DJ::first('SHOW TABLES LIKE :table;', [':table' => $name]);
        return (bool)$result;
    }
    // function exists()

    public function drop(): bool
    {
        $name = $this->table->name;
        if ($this->exists()) {
            $result = DJ::play("DROP TABLE `{$name}`;");
            if ($result) {
                $this->columns = [];
                return true;
            } else {
                throw new DJException("Table '{$name}' is not exists");
            }
        } else {
            throw new DJException("Table '{$this->name}' is not exists");
        }
        return false;
    }
    // function drop()

    public function truncate(): bool
    {
        $name = $this->table->name;
        if ($this->exists()) {
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
        if ($this->exists()) {
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

    public function createIndex(Column $column): void
    {
        $name = $this->table->name;
        if (! $this->exists()) {
            $message = "Table '{$name}' does not exists";
            throw new DJException($message);
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
    // function index()

    public function createTable()
    {
        $name = $this->table->name;
        if ($this->exists()) {
            $sql = "SHOW CREATE TABLE `{$name}`;";
            $setlist = DJ::play($sql);
            return $setlist->first();
        }
        return null;
    }

    public function columns(string $column = null)
    {
        $name = $this->table->name;
        if ($this->exists()) {
            $params = [];
            $sql = "SHOW FULL COLUMNS FROM `{$name}`";
            if ($column) {
                $sql .= " WHERE Field = :column";
                $params['column'] = $column;
            }
            $sql .= ';';
            $setlist = DJ::play($sql, $params);
            if ($column) {
                return $setlist->first();
            } else {
                return $setlist->all();
            }
        }
        return null;
    }

    public function indexes(string $index = null)
    {
        $name = $this->table->name;
        if ($this->exists()) {
            $params = [];
            $sql = "SHOW INDEX FROM `{$name}`";
            if ($index) {
                $sql .= " WHERE Key_name = :index";
                $params['index'] = $index;
            }
            $sql .= ';';
            $setlist = DJ::play($sql, $params);
            if ($index) {
                return $setlist->first();
            } else {
                return $setlist->all();
            }
        }
        return null;
    }
}

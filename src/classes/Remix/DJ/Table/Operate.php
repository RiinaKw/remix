<?php

namespace Remix\DJ\Table;

use Remix\Gear;
use Remix\Instruments\DJ;
use Remix\DJ\Table;

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

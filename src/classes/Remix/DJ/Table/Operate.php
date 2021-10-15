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
}

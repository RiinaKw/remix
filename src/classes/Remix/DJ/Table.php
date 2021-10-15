<?php

namespace Remix\DJ;

use Remix\Gear;
use Remix\Instruments\DJ;
use Remix\DJ\Setlist;
use Remix\DJ\BPM;
use Remix\DJ\BPM\Select;
use Remix\DJ\Columns;
use Remix\Exceptions\DJException;

/**
 * Remix DJ Table : DB tables
 *
 * @package  Remix\DB\Table
 * @todo Write the details.
 */
class Table extends Gear
{
    protected $name;
    protected $comment = '';
    protected $columns = [];

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
                return $this->name;

            default:
                $message = 'Unknown property "' . $key . '"';
                throw new DJException($message);
        }
    }
    // function __get()

    public function exists(): bool
    {
        $result = DJ::first('SHOW TABLES LIKE :table;', [':table' => $this->name]);
        return (bool)$result;
    }
    // function exists()

    public function comment(string $comment)
    {
        $this->comment = $comment;
    }

    public function create(callable $cb): bool
    {
        if (! $this->exists()) {
            $cb($this);
            if (count($this->columns) < 1) {
                $message = "Table '{$this->name}' must contains any column";
                throw new DJException($message);
            }
            $columns_string = [];
            foreach ($this->columns as $column) {
                $columns_string[] = (string)$column;
            }
            $columns = implode(', ', $columns_string);
            $sql = "CREATE TABLE `{$this->name}` ({$columns})";
            if ($this->comment) {
                $sql .= " COMMENT='{$this->comment}'";
            }
            $sql .= ';';

            try {
                if (DJ::play($sql)) {
                    foreach ($this->columns as $column) {
                        $this->createIndex($column);
                    }
                    return true;
                } else {
                    $message = 'Cannot create table "' . $this->name . '"';
                    throw new DJException($message);
                }
            } catch (\Exception $e) {
                throw new DJException($e->getMessage());
            }
        } else {
            $message = 'Table "' . $this->name . '" is already exists';
            throw new DJException($message);
        }
        return false;
    }
    // function create()

    public function createIndex(Column $column): void
    {
        if (! $this->exists()) {
            $message = 'Table "' . $this->name .  '" does not exists';
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
        $index_name = $prefix . '__' . $this->name . '__' . $column->name;

        $sql = "CREATE {$index_type} `{$index_name}` ON `{$this->name}`(`{$column->name}`);";
        $results = DJ::play($sql);
        if (! $results) {
            $message = 'Cannot create index "' . $index_name .  '"for table "' . $this->name . '"';
            throw new DJException($message);
        }
    }
    // function index()

    public function drop(): bool
    {
        if ($this->exists()) {
            $sql = 'DROP TABLE ' . $this->name . ';';
            $result = DJ::play($sql);
            if ($result) {
                $this->columns = [];
                return true;
            } else {
                $message = "Table '{$this->name}' is not exists";
                throw new DJException($message);
            }
        } else {
            $message = "Table '{$this->name}' is not exists";
            throw new DJException($message);
        }
        return false;
    }
    // function drop()

    public function truncate(): bool
    {
        if ($this->exists()) {
            $sql = "TRUNCATE TABLE `{$this->name}`;";
            return DJ::play($sql) !== false;
        } else {
            $message = "Table '{$this->name}' is not exists";
            throw new DJException($message);
        }
    }
    // function truncate()

    public function select(): BPM
    {
        return new Select($this->name);
    }
    // function select()

    public function column(string $column): ?Column
    {
        $def = DJ::dumpColumns($this->name, $column);
        return $def ? Column::constructFromDef($def) : null;
    }
    // function column()

    public function index(string $index): ?Index
    {
        $def = DJ::dumpIndexes($this->name, $index);
        return $def ? Index::constructFromDef($def) : null;
    }

    public function __call(string $type, $args): Column
    {
        $name = $args[0];
        switch ($type) {
            case 'int':
                $column = new Columns\IntCol($name, ($args[1] ?? false));
                break;

            case 'varchar':
                $column = new Columns\VarcharCol($name, ($args[1] ?? false));
                break;

            //case 'text':
            case 'datetime':
                $column = new Columns\DatetimeCol($name);
                break;

            case 'timestamp':
                $column = new Columns\TimestampCol($name);
                break;

            default:
                $message = 'unknown method ' . $type;
                throw new DJException($message);
        }
        $this->columns[$args[0]] = $column;
        return $column;
    }
    // function __call()
}
// class Table

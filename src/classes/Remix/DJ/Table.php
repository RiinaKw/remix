<?php

namespace Remix\DJ;

use Remix\Gear;
use Remix\Instruments\DJ;
use Remix\DJ\Setlist;
use Remix\DJ\BPM;
use Remix\DJ\BPM\Select;
use Remix\DJ\Columns;
use Remix\DJ\Table\Operate;
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

    public function operate(): Operate
    {
        return new Operate($this);
    }

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

    public function comment(string $comment)
    {
        $this->comment = $comment;
    }

    public function append(Column $column, string $after = '')
    {
        $this->columns[$column->name] = $column;
    }

    public function create(callable $cb): bool
    {
        if (! $this->operate()->exists()) {
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

    protected function createIndex(Column $column): void
    {
        if (! $this->operate()->exists()) {
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

    public function select(): BPM
    {
        return new Select($this->name);
    }
    // function select()

    public function column(string $column): ?Column
    {
        $def = $this->operate()->columns($column);
        return $def ? Column::constructFromDef($def) : null;
    }
    // function column()

    public function index(string $index): ?Index
    {
        $def = $this->operate()->indexes($index);
        return $def ? Index::constructFromDef($def) : null;
    }
}
// class Table

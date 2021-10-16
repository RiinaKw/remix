<?php

namespace Remix\DJ;

use Remix\Gear;
use Remix\Instruments\DJ;
use Remix\DJ\MC;
use Remix\DJ\BPM;
use Remix\DJ\BPM\Select;
use Remix\Exceptions\DJException;
use Remix\RemixException;

/**
 * Remix DJ Table : DB table operations
 *
 * @package  Remix\DB
 * @todo Write the details.
 */
class Table extends Gear
{
    /**
     * Name of table
     * @var string
     */
    protected $name = '';

    /**
     * Comments of table
     * @var string
     */
    protected $comment = '';

    /**
     * Columns
     * @var array<Column>
     */
    protected $columns = [];

    /**
     * Columns to alter
     * @var array<Column>
     */
    protected $columns_add = [];

    /**
     * Set up the table
     * @param string $name  Name of table
     */
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

    /**
     * Getter
     * @param  string $key  Name of a key
     * @return mixed
     */
    public function __get(string $key)
    {
        switch ($key) {
            case 'name':
            case 'comment':
            case 'columns':
                return $this->$key;

            default:
                $message = 'Unknown property "' . $key . '"';
                throw new RemixException($message);
        }
    }
    // function __get()

    /**
     * Add comments to this table
     * @param  string $comment  Comments to add
     * @return self             Itself
     */
    public function comment(string $comment): self
    {
        $this->comment = $comment;
        return $this;
    }

    /**
     * Create a column into this table, used in CREATE TABLE
     * @param  Column $column  A column to add
     * @return self            Itself
     */
    public function append(Column $column): self
    {
        $this->columns[$column->name] = $column;
        return $this;
    }

    /**
     * Add a column into this table, used in ALTER TABLE
     * @param  Column $column  A column to add
     * @return self            Itself
     */
    public function addColumn(Column $column): self
    {
        $this->columns_add[$column->name] = $column;
        return $this;
    }

    /**
     * Get SELECT query builder
     * @return BPM  Query builder
     */
    public function select(): BPM
    {
        return new Select($this->name);
    }
    // function select()

    /**
     * Callback of create(), modify()
     * @param  self   $table  Itself
     * @throws RemixException This is a prototype; not meant to be called directly.
     * @see Table::create()
     * @see Table::modify()
     */
    private static function callbackToCreate(self $table): void
    {
        throw new RemixException(__METHOD__ . ' is prototype of callback');
    }

    /**
     * Create a table
     * @param  callable $cb  Callback to create columns
     * @return bool          Successful or not
     * @see Table::callbackToCreate()
     */
    public function create(callable $cb): bool
    {
        MC::expectTableExists($this->name, false);

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

    /**
     * Modify a table
     * @param  callable $cb  Callback to alter columns
     * @return bool          Successful or not
     * @see Table::callbackToCreate()
     */
    public function modify(callable $cb): bool
    {
        MC::expectTableExists($this->name, true);
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

    /**
     * Create an index into this table from the column definition
     * @param Column $column  Target column
     */
    public function createIndex(Column $column): void
    {
        MC::expectTableExists($this->name, true);

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

<?php

namespace Remix\DJ;

// Remix core
use Remix\Gear;
use Remix\Instruments\DJ;
use Remix\DJ\MC;
use Remix\DJ\BPM;
use Remix\DJ\Column;
use Remix\DJ\BPM\Select;
// Utilities
use Utility\Arr;
// Exceptions
use Exception;
use Remix\Exceptions\AppException;
use Remix\Exceptions\DJException;

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
     * @var array<string, Column|array>
     */
    protected $columns = [];

    /**
     * Set up the table
     * @param string $name  Name of table
     */
    public function __construct(string $name)
    {
        DJ::expectIdentifier($name);
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
                return $this->$key;

            default:
                $message = "Unknown property '{$key}'";
                throw new AppException($message);
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
     * Add a column into this table, used in ALTER TABLE
     * @param  Column $column  A column to add
     * @return self            Itself
     */

    public function appendColumn(Column $column): self
    {
        if (isset($this->columns[$column->name])) {
            $table_escaped = DJ::identifier($this->name);
            $column_escaped = DJ::identifier($column->name);
            throw new DJException("Column {$column_escaped} is already exists in {$table_escaped}");
        }
        $this->columns[$column->name] = $column;
        return $this;
    }

    /**
     * Callback of create(), modify()
     * @param  self   $table  Itself
     * @throws CoreException This is a prototype; not meant to be called directly.
     * @see Table::create()
     * @see Table::modify()
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    private static function callbackToCreate(self $table): void
    {
        throw new CoreException(__METHOD__ . ' is prototype of callback');
    }

    /**
     * Create a table
     * @param  callable $cb  Callback to create columns
     * @return bool          Successful or not
     * @see Table::callbackToCreate()
     */
    public function create(callable $cb): bool
    {
        MC::expectTableNotExists($this);

        try {
            $cb($this);
        } catch (Exception $e) {
            throw DJException::create($e->getMessage(), debug_backtrace()[0]);
        }
        if (! MC::tableExists($this->name)) {
        //    throw new DJException("Cannot create table '{$this->name}'");
        }
        if (count($this->columns) < 1) {
            throw new DJException("Table '{$this->name}' must contains any column");
        }
        return MC::tableCreate($this, $this->columns);
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
        MC::expectTableExists($this);
        $cb($this);

        if ($this->columns) {
            return MC::tableModify($this, $this->columns);
        }
        return false;
    }
    // function modify()

    public function drop()
    {
        MC::tableDrop($this->name);
    }
    // function drop()

    /**
     * Rename the column contained in this table
     * @param  string $old  Old column name
     * @param  string $new  New column name
     * @return self         Itself
     */
    public function renameColumn(string $old, string $new): self
    {
        $this->columns[] = [
            'op' => 'rename',
            'old' => $old,
            'new' => $new,
        ];
        return $this;
    }

    /**
     * Drop the column contained in this table
     * @param  string $column  Column name to drop
     * @return self            Itself
     */
    public function dropColumn(string $column): self
    {
        $this->columns[] = [
            'op' => 'drop',
            'old' => $column,
        ];
        return $this;
    }

    /**
     * Get column(s) from table definition
     * @param  string|null   $column  Target column, all targets if null
     * @return null|Column|array<string, Column>
     *             * null : it doesn't exist,
     *             * Column : if the target exists,
     *             * array<Column> : array of Column, if no target specified
     */
    public function columns(string $column = null)
    {
        MC::expectTableExists($this);
        $table_escaped = DJ::identifier($this->name);

        $params = [];
        $sql = "SHOW FULL COLUMNS FROM {$table_escaped}";
        if ($column) {
            $sql .= " WHERE Field = :column";
            $params['column'] = $column;
        }
        $sql .= ';';
        $setlist = DJ::play($sql, $params);

        if ($column) {
            $def = $setlist->first();
            return $def ? Column::constructFromDef($def) : null;
        } else {
            return Arr::map($setlist, function ($item) {
                return [
                    Column::constructFromDef($item),
                    $item['Field'],
                ];
            });
        }
    }
    // function columns()

    /**
     * Get Index(es) from table definition
     * @param  string|null   $index  Target index, all targets if null
     * @return null|Index|array<string, Index>
     *             * null : it doesn't exist,
     *             * Index : if the target exists,
     *             * array<Index> : array of Index, if no target specified
     */
    public function indexes(string $index = null)
    {
        MC::expectTableExists($this);
        $table_escaped = DJ::identifier($this->name);

        $params = [];
        $sql = "SHOW INDEX FROM {$table_escaped}";
        if ($index) {
            $sql .= " WHERE Key_name = :index";
            $params['index'] = $index;
        }
        $sql .= ';';
        $setlist = DJ::play($sql, $params);

        if ($index) {
            $def = $setlist->first();
            return $def ? Index::constructFromDef($def) : null;
        } else {
            return Arr::map($setlist, function ($item) {
                return [
                    Index::constructFromDef($item),
                    $item['Key_name'],
                ];
            });
        }
    }
    // function indexes()
}
// class Table

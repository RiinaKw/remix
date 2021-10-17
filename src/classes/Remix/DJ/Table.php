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
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
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
        MC::expectTableExists($this->name, true);
        $cb($this);

        if ($this->columns) {
            return MC::tableModify($this, $this->columns);
        }
        return false;
    }
    // function modify()

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
}
// class Table

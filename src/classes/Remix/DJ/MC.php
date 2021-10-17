<?php

namespace Remix\DJ;

use Remix\Gear;
use Remix\Instruments\DJ;
use Utility\Arr;
use Remix\RemixException;
use Remix\Exceptions\DJException;

/**
 * Remix MC : DB definition manager
 *
 * @package  Remix\DB\Table
 * @todo Write the details.
 */
class MC extends Gear
{
    /**
     * Don't create an instance
     * @throws RemixException  This class should be used statically
     */
    private function __construct()
    {
        throw new RemixException(__CLASS__ . ' should be used statically');
    }

    /**
     * Don't create an instance
     * @throws RemixException  This class should be used statically
     */
    public function __destruct()
    {
        throw new RemixException(__CLASS__ . ' should be used statically');
    }

    /**
     * Get the name of table
     * @param  Table|string  $table  Table instance or table name
     * @return string                Correct table name
     * @throws DJException           For unknown instance
     */
    private static function tableName($table): string
    {
        if ($table instanceof Table) {
            return $table->name;
        } elseif (is_string($table)) {
            return $table;
        }
        throw new DJException('Invalid table instance : ' . get_class($table));
    }

    /**
     * Does the table exists?
     * @param  Table|string  $table  Table instance or table name
     * @return bool                  Exists or not
     */
    public static function tableExists($table): bool
    {
        $name = static::tableName($table);
        $result = DJ::first('SHOW TABLES LIKE :table;', [':table' => $name]);
        return (bool)$result;
    }
    // function tableExists()

    /**
     * Expect the table to exist / not exist, raise an exception if unexpected
     * @param Table|string  $table   Target table instance or table name
     * @param boolean       $exists  Expect it to exist or not
     * @throws DJException           If not expected
     */
    public static function expectTableExists($table, bool $exists = true): void
    {
        $table_escaped = DJ::identifier(static::tableName($table));

        if ($exists && ! static::tableExists($table)) {
            throw new DJException("Table {$table_escaped} is not exists");
        }
        if (! $exists && static::tableExists($table)) {
            throw new DJException("Table {$table_escaped} is already exists");
        }
    }
    // function expectTableExists()

    /**
     * Callback of tableCreate(), tableModify()
     * @param  Table   $table  Target table instance
     * @throws RemixException This is a prototype; not meant to be called directly.
     * @see MC::tableCreate()
     * @see MC::tableModify()
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    private static function callbackToCreate(Table $table): void
    {
        throw new RemixException(__METHOD__ . ' is prototype of callback');
    }

    /**
     * Create table
     * @param  Table|string  $table  Table instance or table name
     * @param  callable      $cb     Callback to create columns
     * @return Table|null            Table instance
     * @see MC::callbackToCreate()
     */
    public static function tableCreate($table, callable $cb): ?Table
    {
        static::expectTableExists($table, false);

        if (! $table instanceof Table) {
            $table = new Table($table);
        }
        $table_escaped = DJ::identifier($table->name);

        $cb($table);

        if (count($table->columns) < 1) {
            throw new DJException("Table {$table_escaped} must contains any column");
        }

        $columns_sql = Arr::mapImplode($table->columns, ', ', function ($column) {
            return DJ::identifier($column->name) . ' ' . (string)$column;
        });
        $sql = "CREATE TABLE {$table_escaped} ({$columns_sql});";

        try {
            if (DJ::play($sql)) {
                static::eachColumnIndexes($table);
                return $table;
            }
        } catch (\Exception $e) {
            //throw new DJException("Cannot create table {$table_escaped}");
            throw $e;
        }
        return null;
    }
    // function tableCreate()

    /**
     * Alter table
     * @param  Table|string  $table  Table instance or table name
     * @param  callable      $cb     Callback to create columns
     * @return Table|null            Table instance
     * @see MC::callbackToCreate()
     */
    public static function tableModify($table, callable $cb): ?Table
    {
        MC::expectTableExists($table, true);

        if (! $table instanceof Table) {
            $table = new Table($table);
        }
        $table_escaped = DJ::identifier($table->name);

        $cb($table);

        if (! $table->columns) {
            throw new DJException("no changes in {$table_escaped}");
        }

        $columns_sql = Arr::mapImplode($table->columns, ', ', function (&$column) use ($table) {
            if (is_array($column)) {
                switch ($column['op']) {
                    case 'rename':
                        $old_column = static::tableColumns($table, $column['old']);

                        $sql = 'CHANGE COLUMN'
                            . ' ' . DJ::identifier($old_column->name)
                            . ' ' . DJ::identifier($column['new'])
                            . ' ' . (string)$old_column;

                        $table->appendColumn($old_column->rename($column['new']));
                        return $sql;

                    case 'drop':
                        return 'DROP COLUMN'
                            . ' ' . DJ::identifier($column['old']);
                        break;
                }
            } elseif ($column->replace) {
                return 'CHANGE COLUMN'
                    . ' ' . DJ::identifier($column->replace)
                    . ' ' . DJ::identifier($column->name)
                    . ' ' . (string)$column;
            } else {
                $sql = 'ADD COLUMN'
                    . ' ' . DJ::identifier($column->name)
                    . ' ' . (string)$column;
                if ($column->after) {
                    $after = DJ::identifier($column->after);
                    $sql .= " AFTER {$after}";
                }
                return $sql;
            }
        });
        $sql = "ALTER TABLE {$table_escaped} {$columns_sql};";

        try {
            if (DJ::play($sql)) {
                static::eachColumnIndexes($table);
                return $table;
            }
        } catch (\Exception $e) {
            //throw new DJException("Cannot modify table {$table_escaped} : " . $e->getMessage());
            throw $e;
        }
        return null;
    }
    // function tableModify()

    protected static function eachColumnIndexes($table): void
    {
        foreach ($table->columns as $column) {
            if ($column instanceof Column) {
                static::indexCreate($table, $column);
            }
        }
    }

    /**
     * Create an index into this table from the column definition
     * @param Table|string   $table   Table instance or table name
     * @param Column         $column  Target column
     */
    public function indexCreate($table, Column $column): void
    {
        static::expectTableExists($table, true);
        $name = static::tableName($table);

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
                throw new DJException("Unknown index type '{$column->index}'");
        }
        $index_escaped = DJ::identifier($prefix . '__' . $name . '__' . $column->name);
        $table_escaped = DJ::identifier($name);
        $column_escaped = DJ::identifier($column->name);

        $sql = "CREATE {$index_type} {$index_escaped} ON {$table_escaped}({$column_escaped});";
        $results = DJ::play($sql);
        if (! $results) {
            throw new DJException("Cannot create index {$index_escaped} for table {$table_escaped}");
        }
    }
    // function indexCreate()

    /**
     * Drop table
     * @param  Table|string  $table  Table instance or table name
     * @param  boolean       $force  If true, run even if it does not exist
     * @return bool                  Successful or not
     * @throws DJException           If not force and target does not exists, or couldn't be executed
     */
    public static function tableDrop(string $table, bool $force = false): bool
    {
        $table_escaped = DJ::identifier(static::tableName($table));

        if (! $force && ! static::tableExists($table)) {
            throw new DJException("Table {$table_escaped} is not exists");
        }

        $result = DJ::play("DROP TABLE IF EXISTS {$table_escaped};");
        if (! $result) {
            throw new DJException("Table {$table_escaped} is not exists");
        }
        return true;
    }
    // function tableDrop()

    /**
     * Show SQL of CREATE TABLE
     * @param  Table|string  $table  Target table instance or table name
     * @return string                SQL of CREATE TABLE
     */
    public static function tableCreateSql($table): string
    {
        static::expectTableExists($table, true);
        $table_escaped = DJ::identifier(static::tableName($table));

        $sql = "SHOW CREATE TABLE {$table_escaped};";
        return DJ::first($sql)['Create Table'];
    }
    // function tableCreateSql()

    /**
     * Get column(s) from table definition
     * @param  Table|string  $table   Target table instance or table name
     * @param  string|null   $column  Target column, all targets if null
     * @return null|Column|array<string, Column>
     *             * null : it doesn't exist,
     *             * Column : if the target exists,
     *             * array<Column> : no target specified
     */
    public static function tableColumns($table, string $column = null)
    {
        static::expectTableExists($table, true);
        $table_escaped = DJ::identifier(static::tableName($table));

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
    // function tableColumns()

    /**
     * Get Index(es) from table definition
     * @param  Table|string  $table  Target table instance or table name
     * @param  string|null   $index  Target index, all targets if null
     * @return null|Index|array<string, Index>
     *             * null : it doesn't exist,
     *             * Index : if the target exists,
     *             * array<Index> : no target specified
     */
    public static function tableIndexes($table, string $index = null)
    {
        static::expectTableExists($table, true);
        $table_escaped = DJ::identifier(static::tableName($table));

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
    // function tableIndexes()
}

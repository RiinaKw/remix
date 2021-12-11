<?php

namespace Remix\DJ;

// Remix core
use Remix\Gear;
use Remix\Instruments\DJ;
// Utilities
use Utility\Arr;
// Exceptions
use Remix\Exceptions\CoreException;
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
     * @throws CoreException  This class should be used statically
     */
    private function __construct()
    {
        throw new CoreException(__CLASS__ . ' should be used statically');
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
     * Expects the table to exist, and throws an exception if unexpected
     * @param Table|string  $table  Target table instance or table name
     * @throws DJException          If the table does not exist
     */
    public static function expectTableExists($table): void
    {
        $table_escaped = DJ::identifier(static::tableName($table));
        if (! static::tableExists($table)) {
            throw new DJException("Table {$table_escaped} is not exists");
        }
    }

    /**
     * Expects the table to not exist, and throws an exception if unexpected
     * @param Table|string  $table  Target table instance or table name
     * @throws DJException          If the table exists
     */
    public static function expectTableNotExists($table): void
    {
        $table_escaped = DJ::identifier(static::tableName($table));
        if (static::tableExists($table)) {
            throw new DJException("Table {$table_escaped} is already exists");
        }
    }

    /**
     * Create table
     * @param  Table|string   $table    Table instance or table name
     * @param  array<Column>  $columns  Columns contained in the table
     * @return bool                     Successfull or not
     */
    public static function tableCreate($table, array $columns)
    {
        $table_escaped = DJ::identifier(static::tableName($table));

        $columns_sql = Arr::mapImplode($columns, ', ', function ($column) {
            return DJ::identifier($column->name) . ' ' . (string)$column;
        });
        $sql = "CREATE TABLE {$table_escaped} ({$columns_sql});";

        if (DJ::play($sql)) {
            foreach ($columns as $column) {
                static::indexCreate($table, $column);
            }
            return true;
        } else {
            throw new DJException("Cannot create table {$table_escaped}");
        }
        return false;
    }
    // function tableCreate()

    /**
     * Create table
     * @param  Table|string   $table           Table instance or table name
     * @param  array<Column>  $columns_to_add  Columns to add to the table
     * @return bool                            Successfull or not
     */
    public static function tableModify(Table $table, array $columns): bool
    {
        $table_escaped = DJ::identifier(static::tableName($table));

        $columns_sql = Arr::mapImplode($columns, ', ', function (&$column) use ($table) {
            if (is_array($column)) {
                switch ($column['op']) {
                    case 'rename':
                        $old_column = $table->columns($column['old']);

                        $sql = 'CHANGE COLUMN'
                            . ' ' . DJ::identifier($old_column->name)
                            . ' ' . DJ::identifier($column['new'])
                            . ' ' . (string)$old_column;

                        $columns[] = $old_column->rename($column['new']);
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

        if (DJ::play($sql)) {
            foreach ($columns as $column) {
                if ($column instanceof Column) {
                    static::indexCreate($table, $column);
                }
            }
            return true;
        } else {
            throw new DJException("Cannot modify table {$table_escaped}");
        }
        return false;
    }
    // function tableModify()

    /**
     * Create an index into this table from the column definition
     * @param Table|string   $table   Table instance or table name
     * @param Column         $column  Target column
     */
    public static function indexCreate($table, Column $column): void
    {
        static::expectTableExists($table);
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
     * Drop the table
     * @param  Table|string  $table  Table instance or table name
     * @return bool                  Successful or not
     * @throws DJException           If target does not exists
     */
    public static function tableDrop($table): bool
    {
        if (! static::tableExists($table)) {
            $table_escaped = DJ::identifier(static::tableName($table));
            throw new DJException("Table {$table_escaped} is not exists");
        }
        return static::tableDropIfExists($table);
    }
    // function tableDrop()

    /**
     * Drop the table forcibly
     * @param  Table|string  $table  Table instance or table name
     * @return bool                  Successful or not
     */
    public static function tableDropForce($table)
    {
        return static::tableDropIfExists($table);
    }

    /**
     * Drop the table if it exists
     * @param  Table|string  $table  Table instance or table name
     * @return bool                  Successful or not
     * @throws DJException           If couldn't be executed
     */
    protected static function tableDropIfExists($table)
    {
        $table_escaped = DJ::identifier(static::tableName($table));
        $result = DJ::play("DROP TABLE IF EXISTS {$table_escaped};");
        if (! $result) {
            throw new DJException("Failed to drop table {$table_escaped}");
        }
        return true;
    }

    /**
     * Show SQL of CREATE TABLE
     * @param  Table|string  $table  Target table instance or table name
     * @return string                SQL of CREATE TABLE
     */
    public static function tableCreateSql($table): string
    {
        static::expectTableExists($table);
        $table_escaped = DJ::identifier(static::tableName($table));

        $sql = "SHOW CREATE TABLE {$table_escaped};";
        return DJ::first($sql)['Create Table'];
    }
    // function tableCreateSql()
}

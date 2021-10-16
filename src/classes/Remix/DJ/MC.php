<?php

namespace Remix\DJ;

use Remix\Gear;
use Remix\Instruments\DJ;
use Remix\Exceptions\DJException;
use Remix\RemixException;

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
     * Does the table exists?
     * @param  string $table  Target table
     * @return bool           Exists or not
     */
    public static function tableExists(string $table): bool
    {
        $result = DJ::first('SHOW TABLES LIKE :table;', [':table' => $table]);
        return (bool)$result;
    }
    // function tableExists()

    /**
     * Expect the table to exist / not exist, raise an exception if unexpected
     * @param string  $table   Target table
     * @param boolean $exists  Expect it to exist or not
     * @throws DJException     If not expected
     */
    public static function expectTableExists(string $table, bool $exists = true): void
    {
        if ($exists && ! static::tableExists($table)) {
            throw new DJException("Table '{$table}' is not exists");
        }
        if (! $exists && static::tableExists($table)) {
            throw new DJException("Table '{$table}' is already exists");
        }
    }
    // function expectTableExists()

    /**
     * Create table
     * @param  Table          $table    Table instance
     * @param  array<Column>  $columns  Columns contained in the table
     * @return bool                     Successfull or not
     */
    public static function tableCreate($table, array $columns)
    {
        $columns_string = [];
        foreach ($columns as $column) {
            $columns_string[] = (string)$column;
        }
        $columns_sql = implode(', ', $columns_string);
        $sql = "CREATE TABLE `{$table->name}` ({$columns_sql});";

        try {
            if (DJ::play($sql)) {
                foreach ($columns as $column) {
                    static::indexCreate($table, $column);
                }
                return true;
            } else {
                throw new DJException("Cannot create table '{$table->name}'");
            }
        } catch (\Exception $e) {
            throw new DJException($e->getMessage());
        }
        return false;
    }
    // function tableCreate()

    /**
     * Create table
     * @param  Table          $table           Table instance
     * @param  array<Column>  $columns_to_add  Columns to add to the table
     * @return bool                            Successfull or not
     */
    public static function tableModify(Table $table, array $columns_to_add): bool
    {
        $columns_string = [];
        foreach ($columns_to_add as $column) {
            $sql = 'ADD COLUMN ' . (string)$column;
            if ($column->after) {
                $sql .= " AFTER `{$column->after}`";
            }
            $columns_string[] = $sql;
        }
        $columns_sql = implode(', ', $columns_string);
        $sql = "ALTER TABLE `{$table->name}` {$columns_sql};";

        try {
            if (DJ::play($sql)) {
                foreach ($columns_to_add as $column) {
                    static::indexCreate($table, $column);
                }
                return true;
            } else {
                throw new DJException("Cannot modify table '{$table->name}'");
            }
        } catch (\Exception $e) {
            throw new DJException($e->getMessage());
        }
        return false;
    }
    // function tableModify()

    /**
     * Create an index into this table from the column definition
     * @param Column $column  Target column
     */
    public function indexCreate(Table $table, Column $column): void
    {
        static::expectTableExists($table->name, true);

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
        $index_name = $prefix . '__' . $table->name . '__' . $column->name;

        $sql = "CREATE {$index_type} `{$index_name}` ON `{$table->name}`(`{$column->name}`);";
        $results = DJ::play($sql);
        if (! $results) {
            throw new DJException("Cannot create index '{$index_name}' for table '{$table->name}'");
        }
    }
    // function indexCreate()

    /**
     * Drop table
     * @param  string  $table  Target table
     * @param  boolean $force  If true, run even if it does not exist
     * @return bool            Successful or not
     * @throws DJException     If not force and target does not exists, or couldn't be executed
     */
    public static function tableDrop(string $table, bool $force = false): bool
    {
        if (! $force && ! static::tableExists($table)) {
            throw new DJException("Table '{$table}' is not exists");
        }

        $result = DJ::play("DROP TABLE IF EXISTS `{$table}`;");
        if (! $result) {
            throw new DJException("Table '{$table}' is not exists");
        }
        return true;
    }
    // function tableDrop()

    /**
     * Show SQL of CREATE TABLE
     * @param  string $table  Target table
     * @return string         SQL of CREATE TABLE
     */
    public static function tableCreateSql(string $table): string
    {
        static::expectTableExists($table, true);

        $sql = "SHOW CREATE TABLE `{$table}`;";
        return DJ::first($sql);
    }
    // function tableCreateSql()

    /**
     * Get column(s) from table definition
     * @param  string      $table                 Target table
     * @param  string|null $column                Target column, all targets if null
     * @return null|Column|array<string, Column>
     *             * Null : it doesn't exist,
     *             * Column : if the target exists,
     *             * array of Column : no target specified
     */
    public static function tableColumns(string $table, string $column = null)
    {
        static::expectTableExists($table, true);

        $params = [];
        $sql = "SHOW FULL COLUMNS FROM `{$table}`";
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
            $result = [];
            foreach ($setlist as $item) {
                $result[$item['Field']] = Column::constructFromDef($item);
            }
            return $result;
        }
    }
    // function tableColumns()

    /**
     * Get Index(es) from table definition
     * @param  string      $table               Target table
     * @param  string|null $index               Target index, all targets if null
     * @return null|Index|array<string, Index>
     *             * Null : it doesn't exist,
     *             * Index : if the target exists,
     *             * array of Index : no target specified
     */
    public static function tableIndexes(string $table, string $index = null)
    {
        static::expectTableExists($table, true);

        $params = [];
        $sql = "SHOW INDEX FROM `{$table}`";
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
            $result = [];
            foreach ($setlist as $item) {
                $result[$item['Key_name']] = Index::constructFromDef($item);
            }
            return $result;
        }
    }
    // function tableIndexes()
}

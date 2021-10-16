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
}

<?php

namespace Remix\DJ;

use Remix\Gear;
use Remix\Instruments\DJ;
use Remix\Exceptions\DJException;

/**
 * Remix MC : DB definition manager
 *
 * @package  Remix\DB\Table
 * @todo Write the details.
 */
class MC extends Gear
{
    public static function tableExists(string $table): bool
    {
        $result = DJ::first('SHOW TABLES LIKE :table;', [':table' => $table]);
        return (bool)$result;
    }

    public static function expectTableExists(string $table, bool $exists = true): void
    {
        if ($exists && ! static::tableExists($table)) {
            throw new DJException("Table '{$table}' is not exists");
        }
        if (! $exists && static::tableExists($table)) {
            throw new DJException("Table '{$table}' is already exists");
        }
    }

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

    public static function tableCreateSql(): ?string
    {
        if (static::tableExists($table)) {
            $sql = "SHOW CREATE TABLE `{$table}`;";
            return DJ::first($sql);
        }
        return null;
    }

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

        if ($column) {
            $def = DJ::first($sql, $params);
            return $def ? Column::constructFromDef($def) : null;
        } else {
            return DJ::play($sql, $params)->all();
        }
    }

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

        if ($index) {
            $def = DJ::first($sql, $params);
            return $def ? Index::constructFromDef($def) : null;
        } else {
            return DJ::play($sql, $params)->all();
        }
    }
}

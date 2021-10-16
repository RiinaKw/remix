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
            $setlist = DJ::play($sql);
            return $setlist->first();
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
        $setlist = DJ::play($sql, $params);
        if ($column) {
            $def = $setlist->first();
            return $def ? Column::constructFromDef($def) : null;
        } else {
            return $setlist->all();
        }
        return null;
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
        $setlist = DJ::play($sql, $params);
        if ($index) {
            $def = $setlist->first();
            return $def ? Index::constructFromDef($def) : null;
        } else {
            return $setlist->all();
        }
        return null;
    }
}

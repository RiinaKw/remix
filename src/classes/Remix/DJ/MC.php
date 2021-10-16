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
    public static function tableExists(string $name): bool
    {
        $result = DJ::first('SHOW TABLES LIKE :table;', [':table' => $name]);
        return (bool)$result;
    }

    public static function tableDrop(string $name, bool $force = false): bool
    {
        if (! $force && ! static::tableExists($name)) {
            throw new DJException("Table '{$name}' is not exists");
        }

        $result = DJ::play("DROP TABLE IF EXISTS `{$name}`;");
        if (! $result) {
            throw new DJException("Table '{$name}' is not exists");
        }
        return true;
    }
}

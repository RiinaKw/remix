<?php

namespace Remix\Instruments;

use Remix\Instrument;
use Remix\Audio;
use Remix\DJ\Setlist;
use Remix\DJ\Back2back;
use Remix\DJ\Table;

/**
 * Remix DJ : DB access manager
 *
 * @package  Remix\DB
 * @todo Write the details.
 */
class DJ extends Instrument
{
    protected static $connection = null;

    public function __construct()
    {
        parent::__construct();
        static::connect();
    }
    // function __construct()

    public function __destruct()
    {
        static::$connection = null;
        parent::__destruct();
    }
    // function __destruct()

    private static function connect()
    {
        if (! static::$connection) {
            $preset = Audio::getInstance()->preset->get('app.db');
            if ($preset) {
                static::$connection = new \PDO($preset['dsn'], $preset['user'], $preset['password']);
            }
        }

        static::$connection->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
        //static::$connection->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
    }

    public static function prepare(string $sql, array $params = []): Setlist
    {
        static::connect();
        $statement = static::$connection->prepare($sql);
        return new Setlist($statement, $params);
    }
    // function prepare()

    public static function play(string $sql, array $params = []): Setlist
    {
        static::connect();
        $setlist = static::prepare($sql, $params);
        return $setlist->play($params);
    }
    // function play()

    public static function first(string $sql, array $params = [])
    {
        static::connect();
        $setlist = static::prepare($sql, $params);
        return $setlist->first($params);
    }
    // function first()

    public static function back2back(): Back2back
    {
        static::connect();
        return new Back2back(static::$connection);
    }
    // function back2back()

    public static function table(string $name): Table
    {
        static::connect();
        return new Table($name);
    }
    // function table()

    public function dumpCreateTable(string $table)
    {
        if (static::table($table)->exists()) {
            $sql = "SHOW CREATE TABLE `{$table}`;";
            $setlist = static::play($sql);
            return $setlist->first();
        }
        return null;
    }

    public function dumpColumns(string $table, string $column = null)
    {
        if (static::table($table)->exists()) {
            $params = [];
            $sql = "SHOW FULL COLUMNS FROM `{$table}`";
            if ($column) {
                $sql .= " WHERE Field = :column";
                $params['column'] = $column;
            }
            $sql .= ';';
            $setlist = static::play($sql, $params);
            if ($column) {
                return $setlist->first();
            } else {
                return $setlist->all();
            }
        }
        return null;
    }

    public function dumpIndexes(string $table, string $index = null)
    {
        if (static::table($table)->exists()) {
            $params = [];
            $sql = "SHOW INDEX FROM `{$table}`";
            if ($index) {
                $sql .= " WHERE Key_name = :index";
                $params['index'] = $index;
            }
            $sql .= ';';
            $setlist = static::play($sql, $params);
            if ($index) {
                return $setlist->first();
            } else {
                return $setlist->all();
            }
        }
        return null;
    }
}
// class DJ

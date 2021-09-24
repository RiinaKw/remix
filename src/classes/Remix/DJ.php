<?php

namespace Remix;

use Remix\DJ\Setlist;
use Remix\DJ\Back2back;
use Remix\DJ\Table;

/**
 * Remix DJ : DB access manager
 *
 * @package  Remix\DB
 * @todo Write the details.
 */
class DJ extends \Remix\Gear
{
    protected static $connection = null;

    public function __construct()
    {
        parent::__construct();
        if (! static::$connection) {
            $preset = Audio::getInstance()->preset->get('app.db');
            if ($preset) {
                static::$connection = new \PDO($preset['dsn'], $preset['user'], $preset['password']);
            }
        }
    }
    // function __construct()

    public function __destruct()
    {
        static::destroy();
        parent::__destruct();
    }
    // function __destruct()

    public static function prepare(string $sql, array $params = []): Setlist
    {
        $statement = static::$connection->prepare($sql);
        return new Setlist($statement, $params);
    }
    // function prepare()

    public static function play(string $sql, array $params = []): Setlist
    {
        $setlist = static::prepare($sql, $params);
        return $setlist->play($params);
    }
    // function play()

    public static function first(string $sql, array $params = [])
    {
        $setlist = static::prepare($sql, $params);
        return $setlist->first($params);
    }
    // function first()

    public static function back2back(): Back2back
    {
        return new Back2back(static::$connection);
    }
    // function back2back()

    public static function table(string $name): Table
    {
        return new Table($name);
    }
    // function table()

    public function destroy(): void
    {
        static::$connection = null;
    }
    // function destroy()
}
// class DJ

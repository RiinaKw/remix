<?php

namespace Remix\Instruments;

use Remix\Instrument;
use Remix\Audio;
use Remix\DJ\Setlist;
use Remix\DJ\Back2back;
use Remix\DJ\Table;
use Remix\Exceptions\DJException;
use PDO;

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

    public static function expectIdentifier(string $name)
    {
        if (preg_match('/\W/', $name)) {
            $message = "Illegal identifier '{$name}'";
            throw new DJException($message);
        }
    }

    public static function identifier(string $name)
    {
        return "`{$name}`";
    }

    private static function connect()
    {
        if (! static::$connection) {
            $preset = Audio::getInstance()->preset->get('app.db');
            if ($preset) {
                static::$connection = new PDO($preset['dsn'], $preset['user'], $preset['password']);
            }
        }

        static::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        static::$connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        //static::$connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
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
}
// class DJ

<?php

namespace Remix;

use Remix\DJ\Setlist;
use Remix\DJ\Back2back;

/**
 * Remix DJ : DB access manager
 */
class DJ extends \Remix\Component
{
    protected static $connection = null;

    public function __construct()
    {
        $remix = \Remix\App::getInstance();
        if (!static::$connection) {
            $config = $remix->config()->get('env.config.db');
            if ($config) {
                static::$connection = new \PDO($config['dsn'], $config['user'], $config['password']);
            }
        }
        \Remix\App::getInstance()->log(__METHOD__);
        return $this;
    }

    public function __destruct()
    {
        static::destroy();
        \Remix\App::getInstance()->log(__METHOD__);
    }

    public static function prepare(string $sql, array $params = []) : Setlist
    {
        $statement = static::$connection->prepare($sql);
        foreach ($params as $name => $value) {
            $label = ':' . $name;
            $statement->bindParam($label, $value);
        }
        return new Setlist($statement);
    }

    public static function play(string $sql, array $params = []) : array
    {
        $statement = static::$connection->prepare($sql);
        foreach ($params as $name => $value) {
            $label = ':' . $name;
            $statement->bindParam($label, $value);
        }
        $statement->execute();
        return $statement->fetchAll();
    }

    public static function back2back() : Back2back
    {
        return new Back2back(static::$connection);
    }

    public static function destroy()
    {
        static::$connection = null;
    }
} // class DJ

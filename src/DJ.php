<?php

namespace Remix;

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

    public static function prepare($sql, $params = [])
    {
        $statement = static::$connection->prepare($sql);
        foreach ($params as $name => $value) {
            $label = ':' . $name;
            $statement->bindParam($label, $value);
        }
        return new \Remix\DJ\Setlist($statement);
    }

    public static function play($sql, $params = [])
    {
        $statement = static::$connection->prepare($sql);
        foreach ($params as $name => $value) {
            $label = ':' . $name;
            $statement->bindParam($label, $value);
        }
        $statement->execute();
        return $statement->fetchAll();
    }

    public static function destroy()
    {
        static::$connection = null;
    }
} // class DJ

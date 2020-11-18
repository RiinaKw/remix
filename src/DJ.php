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

    protected function __construct()
    {
        parent::__construct();

        if (! static::$connection) {
            $config = \Remix\App::getInstance()->config()->get('env.config.db');
            if ($config) {
                static::$connection = new \PDO($config['dsn'], $config['user'], $config['password']);
            }
        }
        return $this;
    }

    public function __destruct()
    {
        static::destroy();
        parent::__destruct();
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

    public static function truncate(string $table)
    {
        if (strpos($table, '`')) {
            throw new Exceptions\DJException('Invalid table name "' . $table . '"');
        }
        $result = static::$connection->exec('TRUNCATE TABLE `' . $table . '`;');
        return ($result !== false);
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

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
    } // function __construct()

    public function __destruct()
    {
        static::destroy();
        parent::__destruct();
    } // function __destruct()

    public static function prepare(string $sql, array $params = []) : Setlist
    {
        $statement = static::$connection->prepare($sql);
        foreach ($params as $name => $value) {
            $label = ':' . $name;
            $statement->bindParam($label, $value);
        }
        return new Setlist($statement);
    } // function prepare()

    public static function play(string $sql, array $params = []) : array
    {
        $setlist = static::prepare($sql, $params);
        $result = $setlist->play($params);
        return $result;
    } // function play()

    public static function truncate(string $table)
    {
        if (strpos($table, '`')) {
            throw new Exceptions\DJException('Invalid table name "' . $table . '"');
        }

        $sql = sprintf('TRUNCATE TABLE `%s`;', $table);
        return static::play($sql) !== false;
    } // function truncate()

    public static function back2back() : Back2back
    {
        return new Back2back(static::$connection);
    } // function back2back()

    public static function destroy()
    {
        static::$connection = null;
    } // function destroy()
} // class DJ

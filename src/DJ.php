<?php

namespace Remix;

/**
 * Remix DJ : DB access manager
 */
class DJ extends \Remix\Component
{
    public static $connection = null;

    public function __construct()
    {
        $remix = \Remix\App::getInstance();
        $config = $remix->config()->get('env.config.db');
        if ($config) {
            static::$connection = new \PDO($config['dsn'], $config['user'], $config['password']);
        }
        return $this;
    }

    public static function play($sql, $params = [])
    {
        $statement = static::$connection->prepare($sql);
        foreach ($params as $name => $value) {
            $label = ':' . $name;
            $statement->bindParam($label, $value);
        }
        $statement->execute();
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }
} // class DJ

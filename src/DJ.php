<?php

namespace Remix;

use Remix\DJ\Setlist;
use Remix\DJ\Back2back;

/**
 * Remix DJ : DB access manager
 */
class DJ extends \Remix\Gear
{
    protected static $connection = null;

    protected function __construct()
    {
        parent::__construct();
        if (! static::$connection) {
            $preset = App::getInstance()->preset->get('env.db');
            if ($preset) {
                static::$connection = new \PDO($preset['dsn'], $preset['user'], $preset['password']);
            }
        }
        return $this;
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
        foreach ($params as $name => $value) {
            $label = ':' . $name;
            $statement->bindParam($label, $value);
        }
        return new Setlist($statement);
    }
    // function prepare()

    public static function play(string $sql, array $params = []): array
    {
        $setlist = static::prepare($sql, $params);
        $result = $setlist->play($params);
        return $result;
    }
    // function play()

    public static function truncate(string $table): bool
    {
        if (strpos($table, '`')) {
            throw new Exceptions\DJException('Invalid table name "' . $table . '"');
        }

        $sql = sprintf('TRUNCATE TABLE `%s`;', $table);
        return static::play($sql) !== false;
    }
    // function truncate()

    public static function back2back(): Back2back
    {
        return new Back2back(static::$connection);
    }
    // function back2back()

    public function destroy(): void
    {
        static::$connection = null;
    }
    // function destroy()
}
// class DJ

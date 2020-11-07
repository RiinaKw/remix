<?php

namespace Remix;

/**
 * Remix Vinyl : capsulate a single DB record
 */
abstract class Vinyl extends \Remix\Component
{
    public static $table = 'default_table';
    public static $pk = 'default_pk';
    protected $prop = [];

    public function __get($name)
    {
        return $this->prop[$name] ?? null;
    }

    public function __set($name, $value)
    {
        $this->prop[$name] = $value;
    }

    public static function find($id)
    {
        $sql = sprintf('SELECT * FROM `%s` WHERE `%s` = :id;', static::$table, static::$pk);
        $setlist = \Remix\DJ::prepare($sql);
        $result = $setlist->asVinyl(static::class)->play(['id' => $id]);

        switch (count($result) === 1) {
            case 1:
                return $result[0];
                break;
            case 2:
                throw new \Remix\RemixException('find by primary key, why multiple results?');
        }
        return null;
    }
} // class Vinyl

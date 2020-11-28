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

    public function toArray()
    {
        return $this->prop;
    }

    public static function find($id) : ?self
    {
        $sql = sprintf('SELECT * FROM `%s` WHERE `%s` = :id;', static::$table, static::$pk);
        $setlist = \Remix\DJ::prepare($sql);
        $result = $setlist->asVinyl(static::class)->play(['id' => $id]);

        switch (count($result)) {
            case 1:
                return $result[0];
                break;
            case 2:
                throw new \Remix\DJException('find by primary key, why multiple results?');
        }
        return null;
    }
} // class Vinyl

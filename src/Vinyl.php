<?php

namespace Remix;

class Vinyl extends \Remix\Component
{
    protected static $table = 'default_table';
    protected static $pk = 'default_pk';
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
        $setlist = \Remix\DJ::prepare($sql, ['id' => $id]);
        $result = $setlist->asVinyl(static::class)->play();

        switch (count($result) === 1) {
            case 1:
                return $result[0];
                break;
            case 2:
                throw new \Exception('find by primary key, why multiple results?');
        }
        return null;
    }
} // class Vinyl

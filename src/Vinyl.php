<?php

namespace Remix;

/**
 * Remix Vinyl : capsulate a single DB record
 */
abstract class Vinyl extends Gear
{
    public static $table = 'default_table';
    public static $pk = 'default_pk';
    protected $prop = [];
    protected static $turntable = Turntable::class;

    public function __get($name)
    {
        return $this->prop[$name] ?? null;
    }

    public function __set($name, $value)
    {
        $this->prop[$name] = $value;
    }

    public function toArray(): array
    {
        return $this->prop;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function turntable(): Turntable
    {
        return new static::$turntable($this);
    }

    public static function find($id): ?self
    {
        $sql = sprintf('SELECT * FROM `%s` WHERE `%s` = :id;', static::$table, static::$pk);
        $setlist = DJ::prepare($sql);
        $result = $setlist->asVinyl(static::class)->play(['id' => $id]);

        switch (count($result)) {
            case 1:
                return $result[0];
                break;
            case 2:
                throw new DJException('find by primary key, why multiple results?');
        }
        return null;
    }
}
// class Vinyl

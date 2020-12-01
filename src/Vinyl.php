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
        $table = \Remix\DJ::table(static::$table)->where(static::$pk, '=', $id);
        return $table->asVinyl(static::class)->first();
    }
}
// class Vinyl

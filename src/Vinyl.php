<?php

namespace Remix;

use Remix\DJ;
use Remix\DJ\Table;

/**
 * Remix Vinyl : capsulate a single DB record
 */
abstract class Vinyl extends Gear
{
    public static $table = 'default_table';
    public static $pk = 'default_pk';
    protected $prop = [];
    protected $is_new = true;
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

    public static function table(): Table
    {
        return DJ::table(static::$table);
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
        $vinyl = $table->asVinyl(static::class)->first();
        if ($vinyl) {
            $vinyl->is_new = false;
            return $vinyl;
        } else {
            return null;
        }
    }
}
// class Vinyl

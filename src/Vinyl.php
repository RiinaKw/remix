<?php

namespace Remix;

use Remix\DJ;
use Remix\DJ\Table;
use Remix\DJ\BPM;

/**
 * Remix Vinyl : capsulate a single DB record
 */
abstract class Vinyl extends Gear
{
    public static $table = 'default_table';
    public static $pk = 'default_pk';
    protected $prop = [];
    protected $is_new = false;
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

    public static function select($columns = '*'): BPM
    {
        return new BPM\Select(static ::$table, $columns);
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
        $bpm = static::select();
        $bpm->where(static::$pk, '=', $id);
        $setlist = $bpm->prepare();
        $vinyl = $setlist->asVinyl(static::class)->first();
        return $vinyl ?: null;
    }
}
// class Vinyl

<?php

namespace Remix;

use Remix\Instruments\DJ;
use Remix\DJ\Table;
use Remix\DJ\BPM;
use Remix\DJ\Setlist;
use Remix\Turntable;

/**
 * Remix Vinyl : active Record
 *
 * @package  Remix\DB
 */
abstract class Vinyl extends Gear
{
    /**
     * Name of the table
     * @var string
     */
    public const TABLE = 'default_table';

    /**
     * Primary key of the table
     * @var string
     * @todo What if it's a compound primary key?
     */
    public const PK = 'default_pk';

    /**
     * Properties
     * @var array
     */
    protected $prop = [];

    /**
     * Is new record?
     * @var bool
     */
    protected $is_new = false;

    /**
     * Class name of Turntable
     * @var string
     */
    protected static $turntable = Turntable::class;

    /**
     * Get property
     *
     * @param  string $name  Key of property
     * @return mixed         Value of property
     */
    public function __get($name)
    {
        return $this->prop[$name] ?? null;
    }

    /**
     * Set property
     *
     * @param string $name  Key of property
     * @param mixed $value  Value of property
     */
    public function __set($name, $value)
    {
        $this->prop[$name] = $value;
    }

    /**
     * Properties to array
     *
     * @return array  Properties as array
     */
    public function toArray(): array
    {
        return $this->prop;
    }

    /**
     * Get Table object
     *
     * @return DJ\Table  Table object
     */
    public static function table(): Table
    {
        return DJ::table(static::TABLE);
    }

    /**
     * Get Select BPM
     *
     * @param  string $columns  Column name to be retrieved
     * @return BPM              BPM object
     */
    public static function select($columns = '*'): BPM
    {
        return new BPM\Select(static::TABLE, $columns);
    }

    /**
     * Get Turntable
     *
     * @return Turntable  Turntable object
     */
    public function turntable(): Turntable
    {
        $turntable = static::$turntable;
        return new $turntable($this);
    }

    /**
     * Find by primary key
     *
     * @param  mixed $id  Value of primary key
     * @return self|null  Vinyl object
     */
    public static function find($id): ?self
    {
        $bpm = static::select();
        $bpm->where(static::PK, '=', $id);
        $setlist = $bpm->prepare();
        $vinyl = $setlist->asVinyl(static::class)->first();
        return $vinyl ?: null;
    }

    public static function last(): ?self
    {
        $bpm = static::select();
        $bpm->order(static::PK, 'desc');
        $setlist = $bpm->prepare();
        $vinyl = $setlist->asVinyl(static::class)->first();
        return $vinyl ?: null;
    }

    public static function forwardOrder(): Setlist
    {
        $bpm = (static::select())->order(static::PK, 'asc');
        return $bpm->prepare()->asVinyl(static::class)->play();
    }

    public static function reverseOrder(): Setlist
    {
        $bpm = (static::select())->order(static::PK, 'desc');
        return $bpm->prepare()->asVinyl(static::class)->play();
    }
}
// class Vinyl

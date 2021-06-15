<?php

namespace Remix;

use Remix\DJ;
use Remix\Turntable;
use Remix\DJ\BPM;

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
    public static function table(): DJ\Table
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
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function turntable(): Turntable
    {
        return new static::$turntable($this);
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
}
// class Vinyl

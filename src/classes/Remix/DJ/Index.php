<?php

namespace Remix\DJ;

use Remix\Gear;
use Remix\Exceptions\DJException;

/**
 * Remix DJ Index : index definition of DB table
 *
 * @package  Remix\DB\Table
 * @todo Write the details.
 */
class Index extends Gear
{
    protected $table = '';
    protected $name = '';
    protected $props = [];

    public function __construct(string $table, string $name)
    {
        $this->table = $table;
        $this->name = $name;
    }

    public static function constructFromDef(array $def = []): self
    {
        $index = new static($def['Table'], $def['Key_name']);
        $index->props['column'] = $def['Column_name'];
        return $index;
    }

    public function __get(string $key)
    {
        switch ($key) {
            default:
                return $this->props[$key] ?? null;
            case 'table':
                return $this->table;
            case 'name':
                return $this->name;
        }
    }
}

<?php

namespace Remix\DJ\Table;

use Remix\Gear;
use Remix\Instruments\DJ;
use Remix\DJ\MC;
use Remix\DJ\Table;
use Remix\DJ\Column;
use Remix\Exceptions\DJException;

/**
 * Remix DJ Table Operate : SQL manager of Table
 *
 * @package  Remix\DB\Table
 * @todo Write the details.
 */
class Operate extends Gear
{
    protected $table = null;

    public function __construct(Table $table)
    {
        $this->table = $table;
    }

    public function truncate(): bool
    {
        $name = $this->table->name;
        if (MC::tableExists($name)) {
            return DJ::play("TRUNCATE TABLE `{$name}`;") !== false;
        } else {
            $message = "Table '{$name}' is not exists";
            throw new DJException($message);
        }
    }
    // function truncate()
}

<?php

namespace Remix\DJ;

use Remix\Gear;
use Remix\Vinyl;

/**
 * Remix DJ Livehouse : migration
 *
 * @package  Remix\DB
 * @todo Write the details.
 */
class Livehouse extends Gear
{
    protected $name = '';

    protected function __construct($name)
    {
        parent::__construct();

        $this->name = $name;
    }

    public function __get(string $key)
    {
        switch ($key) {
            case 'name':
                return $this->name;
            default:
                return null;
        }
    }

    public function asVinyl(): Vinyl
    {
        $vinyl = new Vinyl\Livehouse();
        $vinyl->name = $this->name;
        return $vinyl;
    }
}
// class Livehouse

<?php

namespace Remix\DJ;

use Remix\Gear;
use Remix\Vinyl;

/**
 * Remix DJ Livehouse : migration
 *
 * @package  Remix\DB
 * @todo Write the details.
 * @todo Can I move some process from the Effector?
 */
abstract class Livehouse extends Gear
{
    protected $name = '';

    public function __construct($name)
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
}
// class Livehouse

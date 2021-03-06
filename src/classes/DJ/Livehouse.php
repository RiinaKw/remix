<?php

namespace Remix\DJ;

use Remix\Gear;

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

    public function asVinyl(): \Remix\Vinyl
    {
        $vinyl = \Remix\Vinyl\Livehouse::factory();
        $vinyl->name = $this->name;
        return $vinyl;
    }
}
// class Livehouse

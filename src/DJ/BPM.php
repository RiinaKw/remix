<?php

namespace Remix\DJ;

use Remix\Gear;

/**
 * Remix BPM : Query Builder
 */
abstract class BPM extends Gear
{
    //private $props = [];
    protected $table;
    protected $context;

/*
    public __construct(string $table, string $context = 'select')
    {
    }
*/

    protected function table($table): self
    {
        $this->table = $table;
        return $this;
    }

    protected function context($context): self
    {
        $this->context = $context;
        return $this;
    }

    abstract public function stringContext();
}
// class BPM

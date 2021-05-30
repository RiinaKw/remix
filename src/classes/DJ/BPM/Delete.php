<?php

namespace Remix\DJ\BPM;

use Remix\DJ\BPM;

/**
 * Remix BPM Select : Query Builder
 */
class Delete extends BPM
{
    public function __construct(string $table)
    {
        parent::__construct();
        $this->context('delete')->table($table);
    }

    protected function buildContext(): string
    {
        return "DELETE FROM `{$this->table}`";
    }
}
// class Delete

<?php

namespace Remix\DJ\BPM;

use Remix\DJ\BPM;

/**
 * Remix BPM Delete : Query Builder for DELETE statement
 *
 * @package  Remix\DB\BPM
 * @todo Write the details.
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

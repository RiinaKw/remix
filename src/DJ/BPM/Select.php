<?php

namespace Remix\DJ\BPM;

use Remix\DJ\BPM;

/**
 * Remix BPM Select : Query Builder
 */
class Select extends BPM
{
    private $columns = [];

    public function __construct(string $table, $columns = '*')
    {
        parent::__construct();
        $this->context('select')->table($table);

        if (is_string($columns)) {
            $this->columns[] = $columns;
        } elseif (is_array($columns)) {
            $this->columns = $columns;
        }
    }

    protected function buildContext(): string
    {
        $selected_arr = [];
        foreach ($this->columns as $column) {
            if ($column === '*') {
                $selected_arr[] = $column;
            } else {
                $selected_arr[] = sprintf('`%s`', $column);
            }
        }
        return sprintf('SELECT %s FROM `%s`', implode(', ', $selected_arr), $this->table);
    }
}
// class Select

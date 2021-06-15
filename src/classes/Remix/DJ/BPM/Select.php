<?php

namespace Remix\DJ\BPM;

use Remix\DJ\BPM;

/**
 * Remix BPM Select : Query Builder for SELECT statement
 *
 * @package  Remix\DB
 * @todo Write the details.
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
                $selected_arr[] = '`' . $column . '`';
            }
        }
        $select_str = implode(', ', $selected_arr);
        return "SELECT {$select_str} FROM `{$this->table}`";
    }
}
// class Select

<?php

namespace Remix\DJ;

use Remix\Gear;
use Remix\Instruments\DJ;
use Remix\DJ\Setlist;

/**
 * Remix BPM : Query Builder
 *
 * @package  Remix\DB
 * @todo Write the details.
 */
abstract class BPM extends Gear
{
    //private $props = [];
    protected $table;
    protected $context;
    protected $where = [];
    protected $order = [];
    protected $holders = [];

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

    public function where(...$args): self
    {
        $holder = null;
        if (is_array($args[0])) {
            $arr = $args[0];
            $op = $arr['op'] ?? '=';
            $value = $arr['value'] ?? null;
            $holder = $arr['holder'] ?? null;
            $column = $arr['column'];
        } elseif (count($args) === 2) {
            $op = '=';
            $column = $args[0];
            $value = $args[1];
        } else {
            $op = $args[1];
            $column = $args[0];
            $value = $args[2];
        }
        if (! $holder) {
            $holder = ':' . md5(rand());
        }
        //$this->holder[$holder] = $value;
        $this->placeholder($holder, $value);
        $this->where[] = "`{$column}` {$op} {$holder}";
        return $this;
    }

    public function order(string $column, string $dir = 'ASC')
    {
        $dir = strtoupper($dir);
        $this->order[$column] = ($dir !== 'DESC' ? 'ASC' : 'DESC');
        return $this;
    }

    protected function buildOrder()
    {
        $arr = [];
        foreach ($this->order as $column => $dir) {
            $arr[] .= "`{$column}` {$dir}";
        }
        return implode(', ', $arr);
    }

    protected function placeholder(string $holder, $value)
    {
        $this->holders[$holder] = $value;
    }

    abstract protected function buildContext();

    protected function buildWhere(): string
    {
        return implode(' AND ', $this->where);
    }

    protected function build(): string
    {
        $sql = $this->buildContext();

        if ($this->where) {
            $sql .= ' WHERE ' . $this->buildWhere();
        }

        if ($this->order) {
            $sql .= ' ORDER BY ' . $this->buildOrder();
        }

        return $sql . ';';
    }

    public function placeholders(): array
    {
        return $this->holders;
    }

    public function prepare(): Setlist
    {
        //return new Setlist($this->build, $this->holders);
        return DJ::prepare($this->build(), $this->holders);
    }
}
// class BPM

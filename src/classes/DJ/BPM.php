<?php

namespace Remix\DJ;

use Remix\Gear;
use Remix\DJ;
use Remix\DJ\Setlist;

/**
 * Remix BPM : Query Builder
 */
abstract class BPM extends Gear
{
    //private $props = [];
    protected $table;
    protected $context;
    protected $where = [];
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

//    public function where($arg1, string $op = '', $value = null): self
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
        $context = $this->buildContext();
        $where = $this->buildWhere();
        return $context . ' WHERE ' . $where . ';';
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

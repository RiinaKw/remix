<?php

namespace Remix\DJ;

use Remix\Gear;
use Remix\DJ;
use Remix\DJ\Setlist;
use Remix\RemixException;

class Table extends Gear
{
    protected $name;
    protected $context = 'select';
    protected $where = [];
    protected $params = [];
    protected $as = null;

    protected function __construct(string $name)
    {
        if (preg_match('/[\'\"\-\`\.\s]/', $name)) {
            $message = sprintf('Illegal table name "%s"', $name);
            throw new RemixException($message);
        }
        parent::__construct();
        $this->name = $name;
    }

    public function exists(): bool
    {
        $result = DJ::play('SHOW TABLES LIKE :table;', [':table' => $this->name]);
        return count($result) > 0;
    }

    public function create(array $columns): bool
    {
        if (! $this->exists()) {
            if (count($columns) < 1) {
                $message = sprintf('Table "%s" must contains any column', $this->name);
                throw new RemixException($message);
            }
            $sql = sprintf('CREATE TABLE `%s` (%s);', $this->name, implode(', ', $columns));
            return DJ::play($sql) !== false;
        } else {
            $message = sprintf('Table "%s" is already exists', $this->name);
            throw new RemixException($message);
        }
    }

    public function drop(): bool
    {
        if ($this->exists()) {
            $sql = sprintf('DROP TABLE `%s`;', $this->name);
            return DJ::play($sql) !== false;
        } else {
            $message = sprintf('Table "%s" is not exists', $this->name);
            throw new RemixException($message);
        }
    }

    public function truncate(): bool
    {
        if ($this->exists()) {
            $sql = sprintf('TRUNCATE TABLE `%s`;', $this->name);
            return DJ::play($sql) !== false;
        } else {
            $message = sprintf('Table "%s" is not exists', $this->name);
            throw new RemixException($message);
        }
    }
    // function truncate()

    public function where($column, $op, $value): self
    {
        $uid = md5(rand());
        $this->where[] = sprintf('`%s` %s :%s', $column, $op, $uid);
        $this->params[$uid] = $value;
        return $this;
    }
    // function where()

    public function asVinyl($vinyl): self
    {
        $this->as = $vinyl;
        return $this;
    }
    // function asVinyl(()

    protected function sql(): string
    {
        $sql = '';
        switch ($this->context) {
            case 'select':
                $sql = sprintf('SELECT * FROM `%s` WHERE %s;', $this->name, implode(' AND ', $this->where));
                break;
        }
        return $sql;
    }
    // function sql()

    public function play()
    {
        $sql = $this->sql();
        $setlist = DJ::prepare($sql, $this->params);
        if ($this->as) {
            $setlist->asVinyl($this->as);
        }
        return $setlist->play($this->params);
    }
    // function play()

    public function first()
    {
        $sql = $this->sql();
        $setlist = DJ::prepare($sql, $this->params);
        if ($this->as) {
            $setlist->asVinyl($this->as);
        }
        return $setlist->first($this->params);
    }
}
// class Table

<?php

namespace Remix\DJ;

use Remix\Gear;
use Remix\DJ;
use Remix\DJ\Setlist;
use Remix\DJ\BPM;
use Remix\DJ\BPM\Select;
use Remix\Exceptions\DJException;

class Table extends Gear
{
    protected $name;
    protected $columns = [];

    protected function __construct(string $name)
    {
        if (preg_match('/\W/', $name)) {
            $message = sprintf('Illegal table name "%s"', $name);
            throw new DJException($message);
        }
        parent::__construct();
        $this->name = $name;
    }
    // function __construct()

    public function exists(): bool
    {
        $result = DJ::first('SHOW TABLES LIKE :table;', [':table' => $this->name]);
        return (bool)$result;
    }
    // function exists()

    public function create(callable $cb): bool
    {
        if (! $this->exists()) {
            $cb($this);
            if (count($this->columns) < 1) {
                $message = sprintf('Table "%s" must contains any column', $this->name);
                throw new DJException($message);
            }
            $columns_string = [];
            foreach ($this->columns as $column) {
                $columns_string[] = (string)$column;
            }
            $sql = sprintf(
                'CREATE TABLE `%s` (%s);',
                $this->name,
                implode(', ', $columns_string)
            );

            try {
                if (DJ::play($sql)) {
                    array_walk($this->columns, function ($column) {
                    });
                    return true;
                } else {
                    $message = 'Cannot create table "' . $this->name . '"';
                    throw new DJException($message);
                }
            } catch (\Exception $e) {
                $this->drop();
                throw new DJException($e->getMessage());
            }
        } else {
            $message = 'Table "' . $this->name . '" is already exists';
            throw new DJException($message);
        }
        return false;
    }
    // function create()

    public function index(Column $column): void
    {
        switch ($column->index) {
            case '':
            case 'pk':
                // ignore
                return;

            case 'index':
                $index_type = 'INDEX';
                $prefix = 'idx';
                break;

            case 'unique':
                $index_type = 'UNIQUE INDEX';
                $prefix = 'uq';
                break;

            default:
                $message = 'Unknown index type "' . $column->index . '"';
                throw new DJException($message);
        }
        $index_name = $prefix . '_' . $this->name . '_' . $column->name;

        $sql = sprintf(
            'CREATE %s `%s` ON %s(%s);',
            $index_type,
            $index_name,
            $this->name,
            $column->name
        );
        if (! DJ::play($sql)) {
            $message = 'Cannot create index for table "' . $this->name . '"';
            throw new DJException($message);
        }
    }
    // function index()

    public function drop(): bool
    {
        if ($this->exists()) {
            $sql = sprintf('DROP TABLE `%s`;', $this->name);
            return DJ::play($sql) !== false;
        } else {
            $message = sprintf('Table "%s" is not exists', $this->name);
            throw new DJException($message);
        }
    }
    // function drop()

    public function truncate(): bool
    {
        if ($this->exists()) {
            $sql = sprintf('TRUNCATE TABLE `%s`;', $this->name);
            return DJ::play($sql) !== false;
        } else {
            $message = sprintf('Table "%s" is not exists', $this->name);
            throw new DJException($message);
        }
    }
    // function truncate()
/*
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
*/
    public function select(): BPM
    {
        return new Select($this->name);
    }

    public function __call(string $name, $args): Column
    {
        switch ($name) {
            case 'int':
            case 'varchar':
                $column = Column::factory($args[0], ['type' => $name, 'length' => $args[1] ?? false]);
                break;

            case 'text':
            case 'datetime':
            case 'timestamp':
                $column = Column::factory($args[0], ['type' => $name]);
                break;

            default:
                $message = sprintf('unknown method "%s"', $name);
                throw new DJException($message);
        }
        $this->columns[$column->name] = $column;
        return $column;
    }
}
// class Table

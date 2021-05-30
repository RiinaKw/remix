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
            /*
            if (count($this->columns) < 1) {
                $message = sprintf('Table "%s" must contains any column', $this->name);
                throw new DJException($message);
            }
            */
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
                        $this->createIndex($column);
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

    public function createIndex(Column $column): void
    {
        if (! $this->exists()) {
            $message = 'Table "' . $index_name .  '" does not exists';
            throw new DJException($message);
        }
        switch ($column->index) {
            case '':
            case 'pk':
                // ignore
                return;

            case 'idx':
                $index_type = 'INDEX';
                $prefix = 'idx';
                break;

            case 'uq':
                $index_type = 'UNIQUE INDEX';
                $prefix = 'uq';
                break;

            default:
                $message = 'Unknown index type "' . $column->index . '"';
                throw new DJException($message);
        }
        $index_name = $prefix . '_' . $this->name . '_' . $column->name;

        $sql = sprintf(
            'CREATE %s `%s` ON `%s`(`%s`);',
            $index_type,
            $index_name,
            $this->name,
            $column->name
        );
        $results = DJ::play($sql);
        if (! $results) {
            $message = 'Cannot create index "' . $index_name .  '"for table "' . $this->name . '"';
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

    public function select(): BPM
    {
        return new Select($this->name);
    }
    // function select()

    public function column(string $name): ?Column
    {
        return $this->columns[$name] ?? null;
    }
    // function column()

    public function __call(string $type, $args): Column
    {
        switch ($type) {
            case 'int':
            case 'varchar':
                $column = Column::factory($args[0], ['type' => $type, 'length' => $args[1] ?? false]);
                break;

            case 'text':
            case 'datetime':
            case 'timestamp':
                $column = Column::factory($args[0], ['type' => $type]);
                break;

            default:
                $message = sprintf('unknown method "%s"', $type);
                throw new DJException($message);
        }
        $this->columns[$args[0]] = $column;
        return $column;
    }
    // function __call()
}
// class Table

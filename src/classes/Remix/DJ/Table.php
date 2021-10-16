<?php

namespace Remix\DJ;

use Remix\Gear;
use Remix\Instruments\DJ;
use Remix\DJ\MC;
use Remix\DJ\BPM;
use Remix\DJ\BPM\Select;
use Remix\DJ\Table\Operate;
use Remix\Exceptions\DJException;

/**
 * Remix DJ Table : DB tables definition
 *
 * @package  Remix\DB\Table
 * @todo Write the details.
 */
class Table extends Gear
{
    protected $name;
    protected $comment = '';
    protected $columns = [];

    protected $indexes_cache = null;

    public function __construct(string $name)
    {
        if (preg_match('/\W/', $name)) {
            $message = "Illegal table name '{$name}'";
            throw new DJException($message);
        }
        parent::__construct();
        $this->name = $name;
    }
    // function __construct()

    public function operate(): Operate
    {
        return new Operate($this);
    }

    public function __get(string $key)
    {
        switch ($key) {
            case 'name':
            case 'comment':
            case 'columns':
                return $this->$key;

            default:
                $message = 'Unknown property "' . $key . '"';
                throw new DJException($message);
        }
    }
    // function __get()

    public function comment(string $comment)
    {
        $this->comment = $comment;
    }

    public function append(Column $column, string $after = '')
    {
        $this->columns[$column->name] = $column;
    }

    public function select(): BPM
    {
        return new Select($this->name);
    }
    // function select()
}
// class Table

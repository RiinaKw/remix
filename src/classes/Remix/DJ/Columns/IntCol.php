<?php

namespace Remix\DJ\Columns;

use Remix\DJ\Column;
use Remix\Exceptions\DJException;

/**
 * Remix DJ Column : column definition of DB table
 *
 * @package  Remix\DB\Table
 * @todo Write the details.
 */
class IntCol extends Column
{
    public function __construct(string $name, int $length = 11, array $params = [])
    {
        $params['type'] = $name;
        $params['length'] = $length;
        parent::__construct($name, 'int', $params);
    }

    public static function fromDef(string $name, int $length, array $def = [])
    {
        $column = new static($name, $length);
        if (strpos(strtolower($def['Type']), 'unsigned') !== false) {
            $column->unsigned();
        }
        return $column;
    }

    public function autoIncrement(): self
    {
        $this->props['additional'][] = 'AUTO_INCREMENT';
        return $this;
    }

    public function __call(string $key, array $arg): self
    {
        switch ($key) {
            case 'unsigned':
                $this->props[$key] = true;
                break;

            default:
                parent::__call($key, $arg);
        }
        return $this;
    }
}

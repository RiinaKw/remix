<?php

namespace Remix\DJ\Columns;

use Remix\DJ\Column;

/**
 * Remix DJ Column : column definition of DB table
 *
 * @package  Remix\DB\Table
 * @todo Write the details.
 */
class VarcharCol extends Column
{
    public function __construct(string $name, int $length, array $params = [])
    {
        $params['type'] = $name;
        $params['length'] = $length;
        parent::__construct($name, 'varchar', $params);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public static function fromDef(string $name, int $length, array $def = [])
    {
        $column = new static($name, $length);
        return $column;
    }
}

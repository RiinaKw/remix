<?php

namespace Remix\DJ\Columns;

use Remix\DJ\Column;

/**
 * Remix DJ Column : column definition of DB table
 *
 * @package  Remix\DB\Table
 * @todo Write the details.
 */
class TextCol extends Column
{
    public function __construct(string $name, array $params = [])
    {
        $params['type'] = $name;
        parent::__construct($name, 'text', $params);
    }
}

<?php

namespace Remix\DJ\Columns;

use Remix\DJ\Column;

/**
 * Remix DJ Column : column definition of DB table
 *
 * @package  Remix\DB\Table
 * @todo Write the details.
 */
class TimestampCol extends Column
{
    public function __construct(string $name, array $params = [])
    {
        $params['type'] = $name;
        parent::__construct($name, 'timestamp', $params);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public static function fromDef(string $name, array $def = [])
    {
        $column = new static($name);
        return $column;
    }

    public function currentTimestamp(): self
    {
        return $this->default('current_timestamp()');
    }
}

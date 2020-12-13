<?php

namespace Remix\DJ;

use Remix\Gear;

/**
 * Remix Back2back : DB transaction manager
 */
class Back2back extends Gear
{
    protected $connection = null;

    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    public function start()
    {
        $this->connection->beginTransaction();
    }

    public function success()
    {
        $this->connection->commit();
    }

    public function fail()
    {
        $this->connection->rollBack();
    }
}
// class Setlist

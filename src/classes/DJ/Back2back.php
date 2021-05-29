<?php

namespace Remix\DJ;

use Remix\Gear;
use Remix\Exceptions\DJException;

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
    // function __construct()

    public function start()
    {
        try {
            $this->fail();
            $this->connection->beginTransaction();
        } catch (\PDOException $e) {
            throw new DJException($e->getMessage());
        }
    }
    // function start()

    public function success()
    {
        if ($this->connection->inTransaction()) {
            try {
                $this->connection->commit();
            } catch (\PDOException $e) {
                throw new DJException($e->getMessage());
            }
        }
    }
    // function success()

    public function fail()
    {
        if ($this->connection->inTransaction()) {
            try {
                $this->connection->rollBack();
            } catch (\PDOException $e) {
                throw new DJException($e->getMessage());
            }
        }
    }
    // function fail()
}
// class Setlist

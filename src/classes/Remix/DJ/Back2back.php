<?php

namespace Remix\DJ;

use Remix\Gear;
use Remix\Exceptions\DJException;

/**
 * Remix Back2back : DB transaction manager
 *
 * @package  Remix\DB
 * @todo Write the details.
 */
class Back2back extends Gear
{
    protected $connection = null;

    public function __construct($connection)
    {
        $this->connection = $connection;
    }
    // function __construct()

    public function __destruct()
    {
        if ($this->inSession()) {
            $this->fail();
            throw new DJException("Back2back is not finished");
        }
    }

    public function start()
    {
        if ($this->inSession()) {
            $this->fail();
            throw new DJException("Back2back has already started");
        }
        $this->connection->beginTransaction();
    }
    // function start()

    public function success()
    {
        if (! $this->inSession()) {
            throw new DJException("Back2back has already finished");
        }
        $this->connection->commit();
    }
    // function success()

    public function fail()
    {
        if (! $this->inSession()) {
            throw new DJException("Back2back has already finished");
        }
        $this->connection->rollBack();
    }
    // function fail()

    public function inSession(): bool
    {
        return $this->connection->inTransaction();
    }

    public function live(callable $cb)
    {
        $this->start();
        try {
            $cb();
        } catch (\Exception $e) {
            $this->fail();
            $message = $e->getMessage();
            $file = $e->getFile();
            $line = $e->getLine();
            throw new DJException("Exception thrown '{$message}'\n{$file}:{$line}");
        }
        $this->success();
    }
}
// class Setlist

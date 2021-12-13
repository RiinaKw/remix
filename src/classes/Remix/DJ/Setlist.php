<?php

namespace Remix\DJ;

// Remix core
use Remix\Gear;
use Remix\Delay;
// Exceptions
use Remix\Exceptions\DJException;
// Utilities
use Utility\Capture;
// Implements
use IteratorAggregate;
use Countable;
use Traversable;
// PDO
use PDO;
use PDOStatement;

/**
 * Remix Setlist : PDO statement
 *
 * @package  Remix\DB
 * @todo Write the details.
 */
class Setlist extends Gear implements IteratorAggregate, Countable
{
    protected $holders;
    protected $statement = null;
    protected $exexuted = '';

    public function __construct(\PDOStatement $statement, array $holders = [])
    {
        parent::__construct();

        $this->holders = $holders;
        $this->statement = $statement;
    }
    // function __construct()

    public function getIterator(): Traversable
    {
        return $this->statement;
    }

    public function asVinyl($classname): self
    {
        $this->statement->setFetchMode(PDO::FETCH_CLASS, $classname);
        return $this;
    }
    // function asVinyl()

    protected function dump(): self
    {
        $dump = Capture::capture(function () {
            $this->statement->debugDumpParams();
        });
        preg_match(
            '/(^|\n)\s*(?<prefix>SQL: \[\d+\])\s+(?<sql>.+)\nParams:/s',
            $dump,
            $matches_sql
        );
        preg_match(
            '/(^|\n)\s*(?<prefix>Sent SQL: \[\d+\])\s+(?<sql>.+)\nParams:/s',
            $dump,
            $matches_sent
        );

        if ($matches_sent) {
            $sql = $matches_sent['sql'];
        } elseif ($matches_sql) {
            $sql = $matches_sql['sql'];
        } else {
            $message = 'Setlist dump failed';
            throw new DJException($message);
        }
        $this->exexuted = $sql;
        Delay::log('QUERY', $sql);
        return $this;
    }
    // function dump()

    public function play(): self
    {
        $this->statement->execute($this->holders);
        return $this->dump();
    }
    // function play()

    public function all(): array
    {
        return $this->statement->fetchAll();
    }

    public function first()
    {
        $result = $this->play();
        return $result ? $this->statement->fetch() : null;
    }

    public function fetch()
    {
        return $this->statement->fetch();
    }

    public function count(): int
    {
        return $this->statement->rowCount();
    }
}
// class Setlist

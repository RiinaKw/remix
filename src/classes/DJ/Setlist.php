<?php

namespace Remix\DJ;

use Remix\Delay;
use Remix\Gear;

/**
 * Remix Setlist : PDO statement
 */
class Setlist extends Gear implements \Iterator, \Countable
{
    protected $holders;
    protected $statement = null;

    private $i = 0;
    private $cur_row = null;

    //public function __construct($statement)
    public function __construct(\PDOStatement $statement, array $holders = [])
    {
        parent::__construct();

        $this->holders = $holders;
        $this->statement = $statement;
    }
    // function __construct()

    public function asVinyl($classname): self
    {
        $this->statement->setFetchMode(\PDO::FETCH_CLASS, $classname);
        return $this;
    }
    // function asVinyl()

    protected function dump(): void
    {
        $dump = \Remix\Utility\Capture::capture(function () {
            $this->statement->debugDumpParams();
        });

        preg_match('/(^|\n)SQL: \[\d+\] (?<sql>.*?)($|\n)/', $dump, $matches_sql);
        preg_match('/(^|\n)Sent SQL: \[\d+\] (?<sql>.*?)($|\n)/', $dump, $matches_sent);
        if ($matches_sent) {
            $sql = $matches_sent['sql'];
        } else {
            $sql = $matches_sql['sql'];
        }
        Delay::getInstance()->log('QUERY', $sql);
    }
    // function dump();

    public function play(): bool
    {
        $result = $this->statement->execute($this->holders);
        $this->dump();

        return $result;
    }
    // function play()

    public function first()
    {
        $result = $this->play();
        return $result ? $this->statement->fetch() : null;
    }

    public function count(): int
    {
        $sql = preg_replace('/^\s*SELECT .+? FROM\s/', 'SELECT COUNT(*) FROM ', $this->statement->queryString);
        $result = \Remix\DJ::first($sql, $this->holders);
        return $result[0];
    }

    public function current()
    {
        return $this->cur_row;
    }

    public function key()
    {
        return $this->i;
    }

    public function next(): void
    {
        $this->cur_row = $this->statement->fetch();
        ++$this->i;
    }

    public function rewind(): void
    {
        $this->cur_row = $this->statement->fetch();
        $this->i = 0;
    }

    public function valid(): bool
    {
        return (bool)$this->cur_row;
    }
}
// class Setlist

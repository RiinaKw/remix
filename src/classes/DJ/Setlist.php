<?php

namespace Remix\DJ;

use Remix\Delay;
use Remix\Gear;
use Remix\Exceptions\DJException;

/**
 * Remix Setlist : PDO statement
 */
class Setlist extends Gear implements \Iterator, \Countable
{
    protected $holders;
    protected $statement = null;
    protected $exexuted = '';

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

    protected function dump(): self
    {
        $dump = \Remix\Utility\Capture::capture(function () {
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
        Delay::getInstance()->log('QUERY', $sql);
        return $this;
    }
    // function dump()

    public function play(): self
    {
        $this->statement->execute($this->holders);
        return $this->dump();
    }
    // function play()

    public function first()
    {
        $result = $this->play();
        return $result ? $this->statement->fetch() : null;
    }

    public function count(): int
    {
        $sql = preg_replace(
            '/^\s*(SELECT)\s+(.+)\s+(FROM)\s/',
            'SELECT COUNT(*) FROM ',
            $this->statement->queryString
        );
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

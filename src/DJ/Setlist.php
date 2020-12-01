<?php

namespace Remix\DJ;

use Remix\Delay;
use Remix\Gear;

/**
 * Remix Setlist : PDO statement
 */
class Setlist extends Gear
{
    protected $statement = null;
    protected $dump = null;

    public function __construct($statement)
    {
        parent::__construct();

        $this->statement = $statement;
    }
    // function __construct()

    public function asVinyl($classname): self
    {
        $this->statement->setFetchMode(\PDO::FETCH_CLASS, $classname);
        return $this;
    }
    // function asVinyl()

    public function play($params = [])
    {
        $this->statement->execute($params);

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

        return $this->statement->fetchAll();
    }
    // function play()
}
// class Setlist

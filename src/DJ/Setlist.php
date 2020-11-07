<?php

namespace Remix\DJ;

/**
 * Remix Setlist : PDO statement
 */
class Setlist extends \Remix\Component
{
    protected $statement = null;

    public function __construct($statement)
    {
        $this->statement = $statement;
    }

    public function asVinyl($vinyl)
    {
        $this->statement->setFetchMode(\PDO::FETCH_CLASS, $vinyl);
        return $this;
    }

    public function play($params = [])
    {
        $this->statement->execute($params);
        return $this->statement->fetchAll();
    }
} // class Setlist

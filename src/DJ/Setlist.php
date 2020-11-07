<?php

namespace Remix\DJ;

class Setlist extends \Remix\Component
{
    protected $statement = null;

    public function __construct($statement)
    {
        $this->statement = $statement;
    }

    public function asVinyl($vinyl = \Remix\Vinyl::class)
    {
        $this->statement->setFetchMode(\PDO::FETCH_CLASS, $vinyl);
        return $this;
    }

    public function play()
    {
        $this->statement->execute();
        return $this->statement->fetchAll();
    }
} // class Setlist
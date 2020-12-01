<?php

namespace App\Livehouse;

use Remix\DJ;
use Remix\DJ\Livehouse;
use Remix\DJ\Table;

class Test2 extends Livehouse
{
    public function open()
    {
        Table::context('test2')->create([
            'id INT',
            'title TEXT',
        ]);
    }

    public function close()
    {
        Table::context('test2')->drop();
    }
}

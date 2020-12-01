<?php

namespace App\Livehouse;

use Remix\DJ;
use Remix\DJ\Livehouse;
use Remix\DJ\Table;

class Test extends Livehouse
{
    public function open()
    {
        Table::context('test')->create([
            'id INT',
            'title TEXT',
        ]);
    }

    public function close()
    {
        Table::context('test')->drop();
    }
}

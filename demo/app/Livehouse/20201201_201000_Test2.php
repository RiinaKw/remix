<?php

namespace App\Livehouse;

use Remix\DJ;
use Remix\DJ\Livehouse;

class Test2 extends Livehouse
{
    public function open()
    {
        DJ::table('test2')->create([
            'id INT',
            'title TEXT',
        ]);
    }

    public function close()
    {
        DJ::table('test2')->drop();
    }
}

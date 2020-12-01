<?php

namespace App\Livehouse;

use Remix\DJ;
use Remix\DJ\Livehouse;

class Test extends Livehouse
{
    public function open()
    {
        DJ::table('test')->create([
            'id INT',
            'title TEXT',
        ]);
    }

    public function close()
    {
        DJ::table('test')->drop();
    }
}

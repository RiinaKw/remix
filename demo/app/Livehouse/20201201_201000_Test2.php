<?php

namespace App\Livehouse;

use Remix\DJ;
use Remix\DJ\Livehouse;

class Test2 extends Livehouse
{
    public function open()
    {
        DJ::play('CREATE TABLE `test2` (`id` INT, `title` TEXT);');
    }

    public function close()
    {
        DJ::play('DROP TABLE `test2`;');
    }
}

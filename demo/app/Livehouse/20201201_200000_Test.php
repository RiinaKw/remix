<?php

namespace App\Livehouse;

use Remix\DJ;
use Remix\DJ\Livehouse;

class Test extends Livehouse
{
    public function open()
    {
        DJ::play('CREATE TABLE `test` (`id` INT, `title` TEXT);');
    }

    public function close()
    {
        DJ::play('DROP TABLE `test`;');
    }
}

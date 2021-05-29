<?php

namespace App\Livehouse;

use Remix\DJ;
use Remix\DJ\Livehouse;
use Remix\DJ\Table;

class Test extends Livehouse
{
    public function open()
    {
        DJ::table('test')->create(function (Table $table) {
            return [
                $table->int('id')->pk(),
                $table->varchar('name', 100),
                $table->text('profile')->nullable(),
                $table->timestamp('created_at')->default('current_timestamp()'),
            ];
        });
    }

    public function close()
    {
        DJ::table('test')->drop();
    }
}

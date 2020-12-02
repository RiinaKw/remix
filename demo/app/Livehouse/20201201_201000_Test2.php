<?php

namespace App\Livehouse;

use Remix\DJ;
use Remix\DJ\Livehouse;
use Remix\DJ\Table;

class Test2 extends Livehouse
{
    public function open()
    {
        DJ::table('test2')->create(function (Table $table) {
            return [
                $table->int('id')->pk(),
                $table->int('user_id')->unsigned()->index(),
                $table->varchar('title', 100)->default('untitled'),
                $table->text('content')->nullable(),
                $table->timestamp('created_at')->default('current_timestamp()'),
            ];
        });
    }

    public function close()
    {
        DJ::table('test2')->drop();
    }
}

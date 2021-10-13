<?php

namespace Remix\Demo\Livehouse;

use Remix\Instruments\DJ;
use Remix\DJ\Livehouse;
use Remix\DJ\Table;
use Remix\RemixException;

class Test2 extends Livehouse
{
    public function open()
    {
        $table = DJ::table('test2');
        $table->create(function (Table $table) {
            $table->int('id')->pk();
            $table->int('user_id')->unsigned()->index();
            $table->varchar('title', 100)->default('untitled')->unique();
            $table->text('content')->nullable();
            $table->timestamp('created_at')->default('current_timestamp()');
        });
        // throw new RemixException('test in Livehouse');
    }

    public function close()
    {
        DJ::table('test2')->drop();
    }
}

<?php

namespace App\Livehouse;

use Remix\DJ;
use Remix\DJ\Livehouse;
use Remix\DJ\Table;
use Remix\RemixException;

class Post extends Livehouse
{
    public function open()
    {
        $table = DJ::table('posts');
        $table->create(function (Table $table) {
            $table->int('id')->pk();
            $table->varchar('title', 100)->default('untitled');
            $table->text('content')->nullable();
            $table->timestamp('created_at')->default('current_timestamp()');
        });
        // throw new RemixException('test in Livehouse');
    }

    public function close()
    {
        DJ::table('posts')->drop();
    }
}

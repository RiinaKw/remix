<?php

namespace RemixDemo\Livehouse;

use Remix\Instruments\DJ;
use Remix\DJ\Livehouse;
use Remix\DJ\Table;
use Remix\DJ\Column;
use Remix\RemixException;

/**
 * Example livehouse.
 *
 * @package  Demo\Livehouses
 */
class Post extends Livehouse
{
    public function open()
    {
        DJ::table('posts')->create(function (Table $table) {
            $table->comment('post table');
            Column::int('id')->pk()->append($table);
            Column::varchar('title', 100)->default('untitled')->append($table);
            Column::text('content')->nullable()->append($table);
            Column::timestamp('created_at')->default('current_timestamp()')->append($table);
        });
        // throw new RemixException('test in Livehouse');
    }

    public function close()
    {
        DJ::table('posts')->drop();
    }
}

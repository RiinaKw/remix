<?php

namespace RemixDemo\Livehouse;

use Remix\Instruments\DJ;
use Remix\DJ\Livehouse;
use Remix\DJ\Table;
use Remix\DJ\Column;

/**
 * Example livehouse.
 *
 * @package  Demo\Livehouses
 */
class Test2 extends Livehouse
{
    public function open()
    {
        DJ::table('test2')->create(function (Table $table) {
            $table->comment('sample table 2');
            Column::int('id')->pk()->append($table);
            Column::int('user_id')->unsigned()->idx()->append($table);
            Column::varchar('title', 100)->default('untitled')->append($table);
            Column::text('content')->nullable()->append($table);
            Column::timestamp('created_at')->default('current_timestamp()')->append($table);
        });
    }

    public function close()
    {
        DJ::table('test2')->drop();
    }
}

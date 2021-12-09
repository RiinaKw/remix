<?php

namespace Remix\Demo\Livehouse;

use Remix\Instruments\DJ;
use Remix\DJ\Livehouse;
use Remix\DJ\Table;
use Remix\DJ\Column;

class Test extends Livehouse
{
    public function open()
    {
        DJ::table('test')->create(function (Table $table) {
            $table->comment('sample table 1');
            Column::int('id')->pk()->append($table);
            Column::varchar('name', 100)->append($table);
            Column::text('profile')->append($table);
            Column::timestamp('created_at')->default('current_timestamp()')->append($table);
        });
    }

    public function close()
    {
        DJ::table('test')->drop();
    }
}

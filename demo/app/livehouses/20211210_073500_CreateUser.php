<?php

namespace RemixDemo\Livehouse;

use Remix\Instruments\DJ;
use Remix\DJ\Livehouse;
use Remix\DJ\Table;
use Remix\DJ\Column;

class CreateUser extends Livehouse
{
    public function open()
    {
        DJ::table('users')->create(function (Table $table) {
            $table->comment('user table');
            Column::int('id')->pk()
                ->append($table);
            Column::varchar('title', 100)->uq()
                ->append($table);
            Column::varchar('email', 100)->uq()
                ->append($table);
            Column::text('content')->nullable()
                ->append($table);
            Column::timestamp('created_at')->default('current_timestamp()')
                ->append($table);
        });
        // throw new RemixException('test in Livehouse');
    }

    public function close()
    {
        DJ::table('users')->drop();
    }
}

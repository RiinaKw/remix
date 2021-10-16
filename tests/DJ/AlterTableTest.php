<?php

namespace Remix\CoreTests;

use PHPUnit\Framework\TestCase;
use Remix\Instruments\DJ;
use Remix\DJ\MC;
use Remix\DJ\Table;
use Remix\DJ\Column;
use Remix\DJ\Index;
use Remix\DJ\Columns;

class AlterTableTest extends TestCase
{
    protected function setUp(): void
    {
        \Remix\Audio::getInstance()->preset->set('app', require('TestEnv.php'));
    }

    public function tearDown(): void
    {
        \Remix\Audio::destroy();
    }

    protected function prepareTable(): void
    {
        MC::tableDrop('test', true);

        $table = DJ::table('test');
        $table->create(function (Table $table) {
            $table->comment('sample table');
            Column::int('id')->unsigned()->comment('sample')
                ->pk()->appendTo($table);
            Column::varchar('user_id', 100)->nullable()->default(0)->comment('of')
                ->uq()->appendTo($table);
            Column::timestamp('created_at')->currentTimestamp()->comment('comment')
                ->idx()->appendTo($table);
        });
    }

    public function testAddColumn(): void
    {
        $this->prepareTable();

        // Now there are 3 columns
        $columns = MC::tableColumns('test');
        $this->assertSame(['id', 'user_id', 'created_at'], array_keys($columns));

        // Add a column
        $table = DJ::table('test');
        $table->modify(function (Table $table) {
            Column::text('description')->modify($table);
        });

        // There should be 4 columns
        $columns = MC::tableColumns('test');
        $this->assertSame(['id', 'user_id', 'created_at', 'description'], array_keys($columns));
    }

    public function testAddColumnWithOrder(): void
    {
        $this->prepareTable();

        // Add a column after id
        $table = DJ::table('test');
        $table->modify(function (Table $table) {
            Column::varchar('name', 100)->modify($table)->after('id');
        });

        // Has order changed?
        $columns = MC::tableColumns('test');
        $this->assertSame(['id', 'name', 'user_id', 'created_at'], array_keys($columns));
    }
}

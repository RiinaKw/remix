<?php

namespace Remix\CoreTests;

use PHPUnit\Framework\TestCase;
use Remix\Instruments\DJ;
use Remix\DJ\MC;
use Remix\DJ\Table;
use Remix\DJ\Column;
use Remix\DJ\Index;
use Remix\DJ\Columns;

class TableTest extends TestCase
{
    protected function setUp(): void
    {
        \Remix\Audio::getInstance()->preset->set('app', require('TestEnv.php'));
    }

    public function tearDown(): void
    {
        \Remix\Audio::destroy();
    }

    public function testTable(): void
    {
        $table = DJ::table('test');
        $this->assertInstanceof(Table::class, $table);
        $this->assertSame('test', $table->name);
    }

    public function testColumns(): void
    {
        MC::tableDrop('test', true);

        $table = DJ::table('test');
        $table->create(function (Table $table) {
            Column::int('id')->unsigned()
                ->appendTo($table);
            Column::varchar('user_id', 100)->nullable()->default(0)
                ->appendTo($table);
            Column::timestamp('created_at')->currentTimestamp()
                ->appendTo($table);
        });

        $columns = DJ::play('SHOW COLUMNS FROM test');
        $this->assertCount(3, $columns);

        $column = $columns->fetch();
        $this->assertSame('id', $column['Field']);
        $this->assertSame('int(10) unsigned', $column['Type']);
        $this->assertSame('NO', $column['Null']);
        $this->assertSame(null, $column['Default']);

        $column = $columns->fetch();
        $this->assertSame('user_id', $column['Field']);
        $this->assertSame('varchar(100)', $column['Type']);
        $this->assertSame('YES', $column['Null']);
        $this->assertSame('0', $column['Default']);

        $column = $columns->fetch();
        $this->assertSame('created_at', $column['Field']);
        $this->assertSame('timestamp', $column['Type']);
        $this->assertSame('NO', $column['Null']);
        $this->assertContains($column['Default'], ['current_timestamp()', 'CURRENT_TIMESTAMP']);
    }

    public function testIndexes()
    {
        MC::tableDrop('test', true);

        $table = DJ::table('test');
        $table->create(function (Table $table) {
            Column::int('id')->unsigned()
                ->pk()->appendTo($table);
            Column::varchar('user_id', 100)->nullable()->default(0)
                ->uq()->appendTo($table);
            Column::timestamp('created_at')->currentTimestamp()
                ->idx()->appendTo($table);
        });

        $columns = DJ::play('SHOW INDEXES FROM test');
        $this->assertCount(3, $columns);

        $column = $columns->fetch();
        $this->assertSame('test', $column['Table']);
        $this->assertSame('PRIMARY', $column['Key_name']);
        $this->assertSame('id', $column['Column_name']);
        $this->assertSame('0', $column['Non_unique']);

        $column = $columns->fetch();
        $this->assertSame('test', $column['Table']);
        $this->assertSame('uq__test__user_id', $column['Key_name']);
        $this->assertSame('user_id', $column['Column_name']);
        $this->assertSame('0', $column['Non_unique']);

        $column = $columns->fetch();
        $this->assertSame('test', $column['Table']);
        $this->assertSame('idx__test__created_at', $column['Key_name']);
        $this->assertSame('created_at', $column['Column_name']);
        $this->assertSame('1', $column['Non_unique']);
    }

    public function testAddColumn(): void
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
        unset($table);

        // Now there are 3 columns
        $columns = MC::tableColumns('test');
        array_walk($columns, function (&$item) {
            $item = $item['Field'];
        });
        $this->assertSame(['id', 'user_id', 'created_at'], $columns);

        // Add a column
        $table = DJ::table('test');
        $table->modify(function (Table $table) {
            Column::text('description')->modify($table);
        });

        // There should be 4 columns
        $columns = MC::tableColumns('test');
        array_walk($columns, function (&$item) {
            $item = $item['Field'];
        });
        $this->assertSame(['id', 'user_id', 'created_at', 'description'], $columns);
    }

    public function testAddColumnWithOrder(): void
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
        unset($table);

        // Add a column after id
        $table = DJ::table('test');
        $table->modify(function (Table $table) {
            Column::varchar('name', 100)->modify($table)->after('id');
        });

        // Has order changed?
        $columns = MC::tableColumns('test');
        array_walk($columns, function (&$item) {
            $item = $item['Field'];
        });
        $this->assertSame(['id', 'name', 'user_id', 'created_at'], $columns);
    }
}

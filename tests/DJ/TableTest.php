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
        DJ::play('DROP TABLE IF EXISTS test');

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
        DJ::play('DROP TABLE IF EXISTS test');

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

    public function testGetColumn(): void
    {
        DJ::play('DROP TABLE IF EXISTS test');

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

        $column = MC::tableColumns('test', 'id');
        $this->assertInstanceof(Columns\IntCol::class, $column);
        $this->assertSame('id', $column->name);
        $this->assertSame('INT', $column->type);
        $this->assertSame(10, $column->length);
        $this->assertSame(true, $column->unsigned);
        $this->assertSame(false, $column->nullable);
        $this->assertSame('sample', $column->comment);

        $column = MC::tableColumns('test', 'user_id');
        $this->assertInstanceof(Columns\VarcharCol::class, $column);
        $this->assertSame('user_id', $column->name);
        $this->assertSame('VARCHAR', $column->type);
        $this->assertSame(100, $column->length);
        $this->assertSame(true, $column->nullable);
        $this->assertSame('0', $column->default);
        $this->assertSame('of', $column->comment);

        $column = MC::tableColumns('test', 'created_at');
        $this->assertInstanceof(Columns\TimestampCol::class, $column);
        $this->assertSame('created_at', $column->name);
        $this->assertSame('TIMESTAMP', $column->type);
        $this->assertSame(false, $column->nullable);
        $this->assertContains($column->default, ['current_timestamp()', 'CURRENT_TIMESTAMP']);
        $this->assertSame('comment', $column->comment);
    }

    public function testGetIndex(): void
    {
        DJ::play('DROP TABLE IF EXISTS test');

        $table = DJ::table('test');
        $table->create(function (Table $table) {
            Column::int('id')->unsigned()->pk()->appendTo($table);
            Column::varchar('user_id', 100)->nullable()->default(0)->uq()->appendTo($table);
            Column::timestamp('created_at')->currentTimestamp()->idx()->appendTo($table);
        });
        unset($table);

        $index = MC::tableIndexes('test', 'uq__test__user_id');
        $this->assertInstanceof(Index::class, $index);
        $this->assertSame('uq__test__user_id', $index->name);
        $this->assertSame('test', $index->table);
        $this->assertSame('user_id', $index->column);
    }

    public function testAddColumn(): void
    {
        DJ::play('DROP TABLE IF EXISTS test');

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
        $table->operate()->modify([
            Column::text('description'),
        ]);

        // There should be 4 columns
        $columns = MC::tableColumns('test');
        array_walk($columns, function (&$item) {
            $item = $item['Field'];
        });
        $this->assertSame(['id', 'user_id', 'created_at', 'description'], $columns);
    }

    public function testAddColumnWithOrder(): void
    {
        DJ::play('DROP TABLE IF EXISTS test');

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
        $table->operate()->modify([
            Column::varchar('name', 100)->after('id'),
        ]);

        // Has order changed?
        $columns = MC::tableColumns('test');
        array_walk($columns, function (&$item) {
            $item = $item['Field'];
        });
        $this->assertSame(['id', 'name', 'user_id', 'created_at'], $columns);
    }
}

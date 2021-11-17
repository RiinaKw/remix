<?php

namespace Remix\CoreTests;

use PHPUnit\Framework\TestCase;
use Remix\DJ\MC;
use Remix\Instruments\DJ;
use Remix\DJ\Table;
use Remix\DJ\Column;
use Remix\DJ\Columns;
use Remix\DJ\Index;
use Remix\Exceptions\DJException;

class MCTest extends TestCase
{
    protected function setUp(): void
    {
        \Remix\Audio::getInstance()->preset->set('app', require('TestEnv.php'));
    }

    public function tearDown(): void
    {
        \Remix\Audio::destroy();
    }

    public function testColumns(): void
    {
        MC::tableDrop('test', true);

        $table = DJ::table('test');
        $table->create(function (Table $table) {
            $table->comment('sample table');
            Column::int('id')->unsigned()->comment('sample')
                ->pk()->append($table);
            Column::varchar('user_id', 100)->nullable()->default(0)->comment('of')
                ->uq()->append($table);
            Column::timestamp('created_at')->currentTimestamp()->comment('comment')
                ->idx()->append($table);
        });

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

    public function testIndexes(): void
    {
        MC::tableDrop('test', true);

        $table = DJ::table('test');
        $table->create(function (Table $table) {
            Column::int('id')->unsigned()->pk()->append($table);
            Column::varchar('user_id', 100)->nullable()->default(0)->uq()->append($table);
            Column::timestamp('created_at')->currentTimestamp()->idx()->append($table);
        });

        $index = MC::tableIndexes('test', 'uq__test__user_id');
        $this->assertInstanceof(Index::class, $index);
        $this->assertSame('uq__test__user_id', $index->name);
        $this->assertSame('test', $index->table);
        $this->assertSame('user_id', $index->column);
    }
}

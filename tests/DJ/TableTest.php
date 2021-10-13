<?php

namespace Remix\CoreTests;

use PHPUnit\Framework\TestCase;
use Remix\Instruments\DJ;
use Remix\DJ\Table;
use Remix\DJ\Column;
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
        $this->assertTrue($table instanceof Table);
        $this->assertSame('test', $table->name);
    }

    public function testCreateAndDrop(): void
    {
        DJ::play('DROP TABLE IF EXISTS test');

        $table = DJ::table('test');
        $table->create(function (Table $table) {
            $table->int('id');
        });
        $this->assertTrue($table->exists());

        $table->drop();
        $this->assertFalse($table->exists());
    }

    public function testColumns(): void
    {
        DJ::play('DROP TABLE IF EXISTS test');

        $table = DJ::table('test');
        $table->create(function (Table $table) {
            $table->int('id')->unsigned();
            $table->varchar('user_id', 100)->nullable()->default(0);
            $table->timestamp('created_at')->currentTimestamp();
        });

        $columns = DJ::play('SHOW COLUMNS FROM test');
        $this->assertSame(3, count($columns));

        $column = $columns->first();
        $this->assertSame('id', $column['Field']);
        $this->assertSame('int(10) unsigned', $column['Type']);
        $this->assertSame('NO', $column['Null']);
        $this->assertSame(null, $column['Default']);

        $columns->next();
        $column = $columns->current();
        $this->assertSame('user_id', $column['Field']);
        $this->assertSame('varchar(100)', $column['Type']);
        $this->assertSame('YES', $column['Null']);
        $this->assertSame('0', $column['Default']);

        $columns->next();
        $column = $columns->current();
        $this->assertSame('created_at', $column['Field']);
        $this->assertSame('timestamp', $column['Type']);
        $this->assertSame('NO', $column['Null']);
        $this->assertSame('current_timestamp()', $column['Default']);
    }

/*
    public function testIndexes()
    {
        DJ::play('DROP TABLE IF EXISTS test');

        $table = DJ::table('test');
        $table->create(function (Table $table) {
            $table->int('id')->unsigned()->pk();
            $table->varchar('user_id', 100)->uq()->nullable()->default(0);
            $table->timestamp('created_at')->idx();
        });

        $columns = DJ::play('SHOW INDEXES FROM test');
        $this->assertSame(3, count($columns));

        $column = $columns->first();
        $this->assertSame('test', $column['Table']);
        $this->assertSame('PRIMARY', $column['Key_name']);
        $this->assertSame('id', $column['Column_name']);
        $this->assertSame('0', $column['Non_unique']);

        $columns->next();
        $column = $columns->current();
        $this->assertSame('test', $column['Table']);
        $this->assertSame('uq__test__user_id', $column['Key_name']);
        $this->assertSame('user_id', $column['Column_name']);
        $this->assertSame('0', $column['Non_unique']);

        $columns->next();
        $column = $columns->current();
        $this->assertSame('test', $column['Table']);
        $this->assertSame('idx__test__created_at', $column['Key_name']);
        $this->assertSame('created_at', $column['Column_name']);
        $this->assertSame('1', $column['Non_unique']);
    }
*/

    public function testGetColumn(): void
    {
        DJ::play('DROP TABLE IF EXISTS test');

        $table = DJ::table('test');
        $table->create(function (Table $table) {
            $table->int('id')->unsigned();
            $table->varchar('user_id', 100)->nullable()->default(0);
            $table->timestamp('created_at');
        });
        unset($table);

        $table = DJ::table('test');
        $column = $table->column('id');
        $this->assertTrue($column instanceof Columns\IntCol);
        $this->assertSame('id', $column->name);
        $this->assertSame('INT', $column->type);
        $this->assertSame(10, $column->length);
        $this->assertSame(true, $column->unsigned);
        $this->assertSame(false, $column->nullable);

        $column = $table->column('user_id');
        $this->assertTrue($column instanceof Columns\VarcharCol);
        $this->assertSame('user_id', $column->name);
        $this->assertSame('VARCHAR', $column->type);
        $this->assertSame(100, $column->length);
        $this->assertSame(true, $column->nullable);
        $this->assertSame('0', $column->default);

        $column = $table->column('created_at');
        $this->assertTrue($column instanceof Columns\TimestampCol);
        $this->assertSame('created_at', $column->name);
        $this->assertSame('TIMESTAMP', $column->type);
        $this->assertSame(false, $column->nullable);
        $this->assertSame('current_timestamp()', $column->default);
    }

/*
    public function testGetIndex(): void
    {
        DJ::play('DROP TABLE IF EXISTS test');

        $table = DJ::table('test');
        $table->create(function (Table $table) {
            $table->int('id')->pk()->unsigned();
            $table->varchar('user_id', 100)->uq();
            $table->timestamp('created_at')->idx();
        });
        unset($table);

        $table = DJ::table('test');
        //$index = $table->index('uq__test__user_id');
        //$this->assertTrue($index instanceof Column);
    }*/
}

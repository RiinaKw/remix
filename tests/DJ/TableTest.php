<?php

namespace Remix\CoreTests;

use PHPUnit\Framework\TestCase;
use Remix\Instruments\DJ;
use Remix\DJ\Table;
use Remix\DJ\Column;

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
            $table->int('id')->pk();
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
            $table->int('id')->pk()->unsigned();
            $table->varchar('user_id', 100)->uq()->nullable()->default(0);
            $table->timestamp('created_at')->idx()->currentTimestamp();
        });

        $columns = DJ::play('SHOW COLUMNS FROM test');
        $this->assertSame(3, count($columns));

        $column = $columns->first();
        $this->assertSame('id', $column['Field']);
        $this->assertSame('int(10) unsigned', $column['Type']);
        $this->assertSame('NO', $column['Null']);
        $this->assertSame('PRI', $column['Key']);
        $this->assertSame(null, $column['Default']);

        $columns->next();
        $column = $columns->current();
        $this->assertSame('user_id', $column['Field']);
        $this->assertSame('varchar(100)', $column['Type']);
        $this->assertSame('YES', $column['Null']);
        $this->assertSame('UNI', $column['Key']);
        $this->assertSame('0', $column['Default']);

        $columns->next();
        $column = $columns->current();
        $this->assertSame('created_at', $column['Field']);
        $this->assertSame('timestamp', $column['Type']);
        $this->assertSame('NO', $column['Null']);
        $this->assertSame('MUL', $column['Key']);
        $this->assertSame('current_timestamp()', $column['Default']);
    }

    public function testIndexes()
    {
        DJ::play('DROP TABLE IF EXISTS test');

        $table = DJ::table('test');
        $table->create(function (Table $table) {
            $table->int('id')->pk()->unsigned();
            $table->varchar('user_id', 100)->uq();
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
}

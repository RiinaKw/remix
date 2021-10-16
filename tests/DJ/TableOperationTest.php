<?php

namespace Remix\CoreTests;

use PHPUnit\Framework\TestCase;
use Remix\Instruments\DJ;
use Remix\DJ\MC;
use Remix\DJ\Table;
use Remix\DJ\Column;
use Remix\Exceptions\DJException;

class TableOperationTest extends TestCase
{
    protected function setUp(): void
    {
        \Remix\Audio::getInstance()->preset->set('app', require('TestEnv.php'));
    }

    public function tearDown(): void
    {
        \Remix\Audio::destroy();
    }

    public function testCreate(): void
    {
        // Make sure to drop 'test'
        MC::tableDrop('test', true);

        // 'test' does not exist
        $this->assertFalse(MC::tableExists('test'));

        $table = DJ::table('test');
        $table->create(function (Table $table) {
            Column::int('id')->appendTo($table);
        });

        // 'test' exists
        $this->assertTrue(MC::tableExists('test'));
    }

    public function testCreateDuplicate()
    {
        $this->expectException(DJException::class);
        $this->expectExceptionMessage("Table `test` is already exists");

        MC::tableDrop('test', true);
        $this->assertFalse(MC::tableExists('test'));

        $table = DJ::table('test');
        $table->create(function (Table $table) {
            Column::int('id')->appendTo($table);
        });

        // Try to create a table that already exists
        $table = DJ::table('test');
        $table->create(function (Table $table) {
            Column::int('id')->appendTo($table);
        });
    }

    public function testDrop(): void
    {
        // Make sure to create 'test'
        if (! MC::tableExists('test')) {
            $table = DJ::table('test');
            $table->create(function (Table $table) {
                Column::int('id')->appendTo($table);
            });
        }
        MC::tableDrop('test');

        // 'test' should not exist
        $this->assertFalse(MC::tableExists('test'));
    }

    public function testDropNonExists(): void
    {
        $this->expectException(DJException::class);
        $this->expectExceptionMessage('Table `test` is not exists');

        // Make sure to drop 'test'
        if (MC::tableExists('test')) {
            MC::tableDrop('test');
        }

        // try to drop 'test' that non-exist
        MC::tableDrop('test');
    }

    public function testForceDrop(): void
    {
        // Make sure to drop 'test'
        if (MC::tableExists('test')) {
            MC::tableDrop('test');
        }

        try {
            // Try to forcibly drop 'test' that non-exist
            MC::tableDrop('test', true);
        } catch (\Exception $e) {
            $this->fail('Unexpected exception has thrown');
        }

        $this->assertTrue((bool)'There was no any exceptions.');
    }

    public function testColumnDuplicate(): void
    {
        $this->expectException(DJException::class);
        $this->expectExceptionMessage("Column `id` is already exists in `test`");

        // Make sure to drop 'test'
        MC::tableDrop('test', true);

        // Try to create multiple columns with the same name
        $table = DJ::table('test');
        $table->create(function (Table $table) {
            Column::int('id')->appendTo($table);
            Column::int('id')->appendTo($table);
        });
    }

    public function testModifyColumnDuplicate(): void
    {
        $this->expectException(DJException::class);
        $this->expectExceptionMessage("Column `id` is already exists in `test`");

        // Make sure to drop 'test'
        MC::tableDrop('test', true);

        // Created without problems
        $table = DJ::table('test');
        $table->create(function (Table $table) {
            Column::int('id')->appendTo($table);
        });

        // Try to add a column that already exists
        $table->modify(function (Table $table) {
            Column::int('id')->appendTo($table);
        });
    }
}

<?php

namespace Remix\CoreTests;

use Utility\Tests\BaseTestCase as TestCase;
use Remix\Instruments\DJ;
use Remix\DJ\MC;
use Remix\DJ\Table;
use Remix\DJ\Column;
// Exception
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
        \Remix\Audio::$dead = false;
    }

    public function testCreate(): void
    {
        MC::tableDropForce('test');

        // 'test' does not exist
        $this->assertFalse(MC::tableExists('test'));

        $table = DJ::table('test');
        $table->create(function (Table $table) {
            Column::int('id')->append($table);
        });

        // 'test' exists
        $this->assertTrue(MC::tableExists('test'));
    }

    public function testCreateDuplicate()
    {
        $this->expectException(DJException::class);
        $this->expectExceptionMessage("Table `test` is already exists");

        MC::tableDropForce('test');
        $this->assertFalse(MC::tableExists('test'));

        $table = DJ::table('test');
        $table->create(function (Table $table) {
            Column::int('id')->append($table);
        });

        // Try to create a table that already exists
        $table = DJ::table('test');
        $table->create(function (Table $table) {
            Column::int('id')->append($table);
        });
    }

    public function testDrop(): void
    {
        // Make sure to create 'test'
        if (! MC::tableExists('test')) {
            $table = DJ::table('test');
            $table->create(function (Table $table) {
                Column::int('id')->append($table);
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
            MC::tableDropForce('test');
        } catch (\Exception $e) {
            $this->fail('Unexpected exception has thrown');
        }

        $this->assertTrue((bool)'There was no any exceptions.');
    }
}

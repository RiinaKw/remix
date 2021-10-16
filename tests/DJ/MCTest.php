<?php

namespace Remix\CoreTests;

use PHPUnit\Framework\TestCase;
use Remix\DJ\MC;
use Remix\Instruments\DJ;
use Remix\DJ\Table;
use Remix\DJ\Column;

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

    public function testTable(): void
    {
        DJ::play('DROP TABLE IF EXISTS test');

        // 'test' does not exist
        $this->assertFalse(MC::tableExists('test'));

        $table = DJ::table('test');
        $table->create(function (Table $table) {
            Column::int('id')->appendTo($table);
        });

        // 'test' exists
        $this->assertTrue(MC::tableExists('test'));
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
        $this->expectException(\Remix\Exceptions\DJException::class);
        $this->expectExceptionMessage('Table \'test\' is not exists');

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
            // try to drop 'test' that non-exist
            MC::tableDrop('test', true);
        } catch (\Exception $e) {
            $this->fail();
        }

        $this->assertTrue((bool)'There was no any exceptions.');
    }
}

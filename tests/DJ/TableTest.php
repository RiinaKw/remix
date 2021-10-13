<?php

namespace Remix\CoreTests;

use PHPUnit\Framework\TestCase;
use Remix\Instruments\DJ;
use Remix\DJ\Table;

class TableTest extends TestCase
{
    protected function setUp(): void
    {
        $audio = \Remix\Audio::getInstance();
        $audio->preset->set('app', require('TestEnv.php'));
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

        DJ::play('DROP TABLE IF EXISTS test');
        $this->assertFalse($table->exists());
    }
}

<?php

namespace Remix\CoreTests;

use Utility\Tests\BaseTestCase as TestCase;
// Target of the test
use Remix\DJ\MC;
// Remix core
use Remix\Audio;
use Remix\Instruments\DJ;
use Remix\DJ\Table;
use Remix\DJ\Column;
// Exception
use Remix\Exceptions\DJException;

class MCTest extends TestCase
{
    protected function setUp(): void
    {
        Audio::getInstance()->preset->set('app', require('TestEnv.php'));
    }

    public function tearDown(): void
    {
        Audio::destroy();
    }

    public function testExistsByString(): void
    {
        MC::tableDropForce('test');

        $table = DJ::table('test');
        $table->create(function (Table $table) {
            $table->comment('sample table');
            Column::int('id')->unsigned()->comment('sample')
                ->pk()->append($table);
        });

        $this->assertTrue(MC::tableExists('test'));
    }

    public function testExistsByObject(): void
    {
        MC::tableDropForce('test');

        $table = DJ::table('test');
        $table->create(function (Table $table) {
            $table->comment('sample table');
            Column::int('id')->unsigned()->comment('sample')
                ->pk()->append($table);
        });

        $this->assertTrue(MC::tableExists($table));
    }

    public function testNotExistsByString(): void
    {
        MC::tableDropForce('test');

        $this->assertFalse(MC::tableExists('test'));
    }

    public function testNotExistsByObject(): void
    {
        MC::tableDropForce('test');

        $table = DJ::table('test');
        $this->assertFalse(MC::tableExists($table));
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testNoExceptionIfExists(): void
    {
        MC::tableDropForce('test');

        DJ::table('test')->create(function (Table $table) {
            $table->comment('sample table');
            Column::int('id')->unsigned()->comment('sample')
                ->pk()->append($table);
        });

        MC::expectTableExists('test');
    }

    public function testExceptionIfExists(): void
    {
        $this->expectException(DJException::class);
        $this->expectExceptionMessage("Table `test` is already exists");

        MC::tableDropForce('test');

        DJ::table('test')->create(function (Table $table) {
            $table->comment('sample table');
            Column::int('id')->unsigned()->comment('sample')
            ->pk()->append($table);
        });

        MC::expectTableNotExists('test');
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testNoExceptionIfNotExists(): void
    {
        MC::tableDropForce('test');

        MC::expectTableNotExists('test');
    }

    public function testExceptionIfNotExists(): void
    {
        $this->expectException(DJException::class);
        $this->expectExceptionMessage("Table `test` is not exists");

        MC::tableDropForce('test');

        MC::expectTableExists('test');
    }
}

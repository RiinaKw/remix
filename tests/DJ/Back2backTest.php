<?php

namespace Remix\CoreTests;

use PHPUnit\Framework\TestCase;
use Remix\Instruments\DJ;
use Remix\DJ\MC;
use Remix\DJ\Table;
use Remix\DJ\Column;
use Remix\Exceptions\DJException;

class Back2backTest extends TestCase
{
    protected function setUp(): void
    {
        \Remix\Audio::getInstance()->preset->set('app', require('TestEnv.php'));

        MC::tableDrop('test', true);

        $table = DJ::table('test');
        $table->create(function (Table $table) {
            $table->comment('sample table');
            Column::int('id')->append($table);
        });
    }

    public function tearDown(): void
    {
        \Remix\Audio::destroy();
    }

    public function testSuccess()
    {
        // Make sure the table is empty
        DJ::play('TRUNCATE TABLE test;');
        $setlist = DJ::play('SELECT * FROM test;');
        $this->assertCount(0, $setlist);

        $back2back = DJ::Back2back();

        $back2back->start();
        DJ::play('INSERT INTO test(id) VALUES (1);');
        $back2back->success();

        // Is there contains a row?
        $setlist = DJ::play('SELECT * FROM test;');
        $this->assertCount(1, $setlist);
    }

    public function testFail()
    {
        // Make sure the table is empty
        DJ::play('TRUNCATE TABLE test;');
        $setlist = DJ::play('SELECT * FROM test;');
        $this->assertCount(0, $setlist);

        $back2back = DJ::Back2back();

        $back2back->start();
        DJ::play('INSERT INTO test(id) VALUES (1);');
        $back2back->fail();

        // Hasn't it changed?
        $setlist = DJ::play('SELECT * FROM test;');
        $this->assertCount(0, $setlist);
    }

    public function testDuplicateStart()
    {
        $this->expectException(DJException::class);
        $this->expectExceptionMessage("Back2back has already started");

        $back2back = DJ::Back2back();
        $back2back->start();
        $back2back->start();
        $back2back->success();
    }

    public function testDuplicateSuccess()
    {
        $this->expectException(DJException::class);
        $this->expectExceptionMessage("Back2back has already finished");

        $back2back = DJ::Back2back();
        $back2back->start();
        $back2back->success();
        $back2back->success();
    }

    public function testDuplicateFail()
    {
        $this->expectException(DJException::class);
        $this->expectExceptionMessage("Back2back has already finished");

        $back2back = DJ::Back2back();
        $back2back->start();
        $back2back->fail();
        $back2back->fail();
    }

    public function testDestruct()
    {
        try {
            $back2back = DJ::Back2back();

            $back2back->start();
            DJ::play('INSERT INTO test(id) VALUES (1);');
            $back2back->success();

            unset($back2back);
        } catch (\Exception $e) {
            $this->fail('Unexpected exception has thrown');
        }

        $this->assertTrue((bool)'There was no any exceptions.');
    }

    public function testDestructUnfinished()
    {
        $this->expectException(DJException::class);
        $this->expectExceptionMessage("Back2back is not finished");

        $setlist = DJ::play('SELECT * FROM test;');

        $back2back = DJ::Back2back();
        $back2back->start();
        DJ::play('INSERT INTO test(id) VALUES (1);');

        unset($back2back);
    }

    public function testCallback()
    {
        // Make sure the table is empty
        DJ::play('TRUNCATE TABLE test;');
        $setlist = DJ::play('SELECT * FROM test;');
        $this->assertCount(0, $setlist);

        DJ::Back2back()->live(function () {
            DJ::play('INSERT INTO test(id) VALUES (1);');
        });

        // Is there contains a row?
        $setlist = DJ::play('SELECT * FROM test;');
        $this->assertCount(1, $setlist);
    }

    public function testExceptionInCallback()
    {
        $this->expectException(DJException::class);
        $this->expectExceptionMessage("Exception thrown 'test'");

        DJ::Back2back()->live(function () {
            throw new \Exception('test');
        });
    }
}

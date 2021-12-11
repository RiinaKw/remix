<?php

namespace Remix\CoreTests;

use PHPUnit\Framework\TestCase;
// Target of the test
use Remix\DJ\BPM;
use Remix\DJ\BPM\Select;
use Remix\DJ\BPM\Delete;
// Remix core
use Remix\DJ\Table;
// Utility
use Utility\Reflection\ReflectionObject;

class BPMTest extends TestCase
{
    public function testSelect(): void
    {
        $bpm = new Select('test', 'id');

        $sql = (new ReflectionObject($bpm))->executeMethod('buildContext');
        $this->assertSame('SELECT `id` FROM `test`', $sql);

        $bpm = new Select('test2', ['created_at', 'updated_at']);
        $sql = (new ReflectionObject($bpm))->executeMethod('buildContext');
        $this->assertSame('SELECT `created_at`, `updated_at` FROM `test2`', $sql);

        $bpm = new Select('test3', ['id', '*']);
        $sql = (new ReflectionObject($bpm))->executeMethod('buildContext');
        $this->assertSame('SELECT `id`, * FROM `test3`', $sql);
    }

    public function testDelete(): void
    {
        $bpm = new Delete('test');
        $sql = (new ReflectionObject($bpm))->executeMethod('buildContext');
        $this->assertSame('DELETE FROM `test`', $sql);
    }

    public function testFromTable()
    {
        $bpm = BPM::select(new Table('test'));
        $sql = (new ReflectionObject($bpm))->executeMethod('buildContext');
        $this->assertSame('SELECT * FROM `test`', $sql);
    }

    public function testWhere(): void
    {
/*
        // name and value
        $bpm = new Select('test', 'name');
        $bpm->where('id', 1);
        $sql = (new ReflectionObject($bpm))->executeMethod('buildWhere');
        $this->assertSame("`id` = '1'", $sql);

        // name, op and value
        $bpm = new Select('test', 'name');
        $bpm->where('id', '!=', 1);
        $sql = (new ReflectionObject($bpm))->executeMethod('buildWhere');
        $this->assertSame("`id` <> '1'", $sql);

        // name, null as value
        $bpm = new Select('test', 'name');
        $bpm->where('id', null);
        $sql = (new ReflectionObject($bpm))->executeMethod('buildWhere');
        $this->assertSame("`id` IS NULL", $sql);

        // array as options
        $bpm = new Select('test', 'name');
        $bpm->where([
            'column' => 'id',
            'value' => 3,
        ]);
        $sql = (new ReflectionObject($bpm))->executeMethod('buildWhere');
        $this->assertSame("`id` = '3'", $sql);

        // array with op
        $bpm = new Select('test', 'name');
        $bpm->where([
            'column' => 'id',
            'op' => '!=',
            'value' => null,
        ]);
        $sql = (new ReflectionObject($bpm))->executeMethod('buildWhere');
        $this->assertSame("`id` IS NOT NULL", $sql);

        // array with placeholder
        $bpm = new Select('test', 'name');
        $bpm->where([
            'column' => 'id',
            'placeholder' => 'find_id',
        ]);
        $sql = (new ReflectionObject($bpm))->executeMethod('buildWhere');
        $this->assertSame("`id` = :find_id", $sql);
*/


        // Set up the Reflection
        $bpm = new Select('test', 'name');
        $reflection = new ReflectionObject($bpm);

        $bpm->where('id', 1);
        $sql = $reflection->executeMethod('buildWhere');
        $this->assertRegExp("/^`id` = :[0-9a-zA-Z]+$/", $sql);

        preg_match('/(:[0-9a-zA-Z]+)$/', $sql, $matches);
        $holder = $matches[1];
        //$holders = $bpm->placeholders();

        $holders = $reflection->getProp('holders');
        $this->assertSame(1, $holders[$holder]);
    }
}
// class TrackTest

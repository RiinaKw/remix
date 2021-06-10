<?php

namespace Remix\CoreTests;

use PHPUnit\Framework\TestCase;
use Remix\DJ\Table;
use Remix\DJ\BPM\Select;
use Remix\DJ\BPM\Delete;

class BPMTest extends TestCase
{
    use \Utility\Tests\InvokePrivateBehavior;

    public function testSelect(): void
    {
        $bpm = new Select('test', 'id');
        $sql = $this->invokeMethod($bpm, 'buildContext');
        $this->assertSame('SELECT `id` FROM `test`', $sql);

        $bpm = new Select('test2', ['created_at', 'updated_at']);
        $sql = $this->invokeMethod($bpm, 'buildContext');
        $this->assertSame('SELECT `created_at`, `updated_at` FROM `test2`', $sql);

        $bpm = new Select('test3', ['id', '*']);
        $sql = $this->invokeMethod($bpm, 'buildContext');
        $this->assertSame('SELECT `id`, * FROM `test3`', $sql);
    }

    public function testDelete(): void
    {
        $bpm = new Delete('test');
        $sql = $this->invokeMethod($bpm, 'buildContext');
        $this->assertSame('DELETE FROM `test`', $sql);
    }

    public function testFromTable()
    {
        $bpm = Table::factory('test')->select();
        $sql = $this->invokeMethod($bpm, 'buildContext');
        $this->assertSame('SELECT * FROM `test`', $sql);
    }

    public function testWhere(): void
    {
/*
        // name and value
        $bpm = new Select('test', 'name');
        $bpm->where('id', 1);
        $sql = $this->invokeMethod($bpm, 'buildWhere');
        $this->assertSame("`id` = '1'", $sql);

        // name, op and value
        $bpm = new Select('test', 'name');
        $bpm->where('id', '!=', 1);
        $sql = $this->invokeMethod($bpm, 'buildWhere');
        $this->assertSame("`id` <> '1'", $sql);

        // name, null as value
        $bpm = new Select('test', 'name');
        $bpm->where('id', null);
        $sql = $this->invokeMethod($bpm, 'buildWhere');
        $this->assertSame("`id` IS NULL", $sql);

        // array as options
        $bpm = new Select('test', 'name');
        $bpm->where([
            'column' => 'id',
            'value' => 3,
        ]);
        $sql = $this->invokeMethod($bpm, 'buildWhere');
        $this->assertSame("`id` = '3'", $sql);

        // array with op
        $bpm = new Select('test', 'name');
        $bpm->where([
            'column' => 'id',
            'op' => '!=',
            'value' => null,
        ]);
        $sql = $this->invokeMethod($bpm, 'buildWhere');
        $this->assertSame("`id` IS NOT NULL", $sql);

        // array with placeholder
        $bpm = new Select('test', 'name');
        $bpm->where([
            'column' => 'id',
            'placeholder' => 'find_id',
        ]);
        $sql = $this->invokeMethod($bpm, 'buildWhere');
        $this->assertSame("`id` = :find_id", $sql);
*/
        //
        $bpm = new Select('test', 'name');
        $bpm->where('id', 1);
        $sql = $this->invokeMethod($bpm, 'buildWhere');
        $this->assertRegExp("/^`id` = :[0-9a-zA-Z]+$/", $sql);
        preg_match('/(:[0-9a-zA-Z]+)$/', $sql, $matches);
        $holder = $matches[1];
        //$holders = $bpm->placeholders();
        $holders = $this->invokeProperty($bpm, 'holders');
        $this->assertSame(1, $holders[$holder]);
    }
}
// class TrackTest

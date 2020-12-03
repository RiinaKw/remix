<?php

namespace Remix\CoreTests;

use PHPUnit\Framework\TestCase;
use Remix\DJ\BPM\Select;
use Remix\DJ\BPM\Delete;

class BPMTest extends TestCase
{
    public function testSelect(): void
    {
        $bpm = new Select('test', 'id');
        $this->assertSame('SELECT `id` FROM `test`', $bpm->stringContext());

        $bpm = new Select('test2', ['created_at', 'updated_at']);
        $this->assertSame('SELECT `created_at`, `updated_at` FROM `test2`', $bpm->stringContext());

        $bpm = new Select('test3', ['id', '*']);
        $this->assertSame('SELECT `id`, * FROM `test3`', $bpm->stringContext());
    }

    public function testDelete(): void
    {
        $bpm = new Delete('test');
        $this->assertSame('DELETE FROM `test`', $bpm->stringContext());
    }
}
// class TrackTest

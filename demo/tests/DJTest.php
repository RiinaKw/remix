<?php

namespace Remix\AppTests;

use PHPUnit\Framework\TestCase;

class DJTest extends TestCase
{
    use \Remix\Utility\Tests\InvokePrivateBehavior;

    protected $remix = null;

    protected function setUp() : void
    {
        $this->remix = \Remix\App::getInstance()->initialize(__DIR__ . '/..');
    }

    public function tearDown() : void
    {
        \Remix\App::destroy();
    }

    public function testInstance()
    {
        $connection = $this->staticProperty(\Remix\DJ::class, 'connection');

        $this->assertTrue((bool)$connection);
        $this->assertTrue($connection instanceof \PDO);
    }

    public function testPlay()
    {
        // is SQL executable?
        $result = \Remix\DJ::play('SELECT * FROM users;');
        $this->assertSame(2, count($result));
        $this->assertTrue($result[0] instanceof \Remix\Vinyl);

        // is SQL executable with placeholder?
        $result = \Remix\DJ::play('SELECT * FROM users WHERE id = :id;', ['id' => 1]);
        $this->assertSame(1, count($result));
        $this->assertSame('Riina', $result[0]->name);

        // use setlist
        $setlist = \Remix\DJ::prepare('SELECT * FROM users;');
        $this->assertTrue($setlist instanceof \Remix\DJ\Setlist);

        $result = $setlist->asVinyl(\App\Vinyl\User::class)->play();
        $this->assertTrue($result[0] instanceof \App\Vinyl\User);
    }

    public function testVinyl()
    {
        $vinyl = \App\Vinyl\User::find(1);
        $this->assertTrue((bool)$vinyl);
        $this->assertTrue($vinyl instanceof \App\Vinyl\User);
        $this->assertSame('Riina', $vinyl->name);

        $vinyl = \App\Vinyl\Note::find(1);
        $this->assertTrue((bool)$vinyl);
        $this->assertTrue($vinyl instanceof \App\Vinyl\Note);
        $this->assertSame('Riina Kwaad', $vinyl->body);
    }
}

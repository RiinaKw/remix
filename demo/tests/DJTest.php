<?php

namespace Remix\AppTests;

use PHPUnit\Framework\TestCase;

class DJTest extends TestCase
{
    use \Remix\Utility\Tests\InvokePrivateBehavior;

    protected $remix = null;

    protected function setUp(): void
    {
        $this->remix = \Remix\App::getInstance()->initialize(__DIR__ . '/..');
    }

    public function tearDown(): void
    {
        \Remix\App::destroy();
    }

    public function testInstance(): void
    {
        $connection = $this->invokeStaticProperty(\Remix\DJ::class, 'connection');

        $this->assertTrue((bool)$connection);
        $this->assertTrue($connection instanceof \PDO);
    }

    public function testPlay()
    {
        \Remix\DJ::truncate('users');
        \Remix\DJ::play('INSERT INTO users(name) VALUES(:name);', ['name' => 'Riina']);

        // is SQL executable?
        $result = \Remix\DJ::play('SELECT * FROM users WHERE id = :id;', ['id' => 1]);
        $this->assertSame(1, count($result));
        $this->assertSame('Riina', $result[0]['name']);

        // use setlist
        $setlist = \Remix\DJ::prepare('SELECT * FROM users;');
        $this->assertTrue($setlist instanceof \Remix\DJ\Setlist);

        $result = $setlist->asVinyl(\App\Vinyl\User::class)->play();
        $this->assertTrue($result[0] instanceof \App\Vinyl\User);

        // use setlist with placeholder
        $setlist = \Remix\DJ::prepare('SELECT * FROM users WHERE id = :id;');
        $result = $setlist->asVinyl(\App\Vinyl\User::class)->play(['id' => 1]);
        $this->assertSame(1, count($result));
        $this->assertTrue($result[0] instanceof \App\Vinyl\User);
    }

    public function testVinyl()
    {
        // User vinyl
        $vinyl = \App\Vinyl\User::find(1);
        $this->assertTrue((bool)$vinyl);
        $this->assertTrue($vinyl instanceof \App\Vinyl\User);
        $this->assertSame('Riina', $vinyl->name);

        // Note vinyl
        $vinyl = \App\Vinyl\Note::find(1);
        $this->assertTrue((bool)$vinyl);
        $this->assertTrue($vinyl instanceof \App\Vinyl\Note);
        $this->assertSame('Riina Kwaad', $vinyl->body);

        // not found
        $vinyl = \App\Vinyl\Note::find(10000);
        $this->assertNull($vinyl);
    }

    public function testInsert()
    {
        // get current count
        $result = \Remix\DJ::play('SELECT * FROM users;');
        $count = count($result);

        // insert
        $result = \Remix\DJ::play('INSERT INTO users(name) VALUES(:name);', ['name' => 'Luke']);

        // get new count
        $result = \Remix\DJ::play('SELECT * FROM users;');
        $this->assertSame($count + 1, count($result));
    }

    public function testTruncate()
    {
        $result = \Remix\DJ::truncate('users');
        $this->assertTrue($result);

        $result = \Remix\DJ::play('SELECT * FROM users;');
        $this->assertSame(0, count($result));
    }

    public function testTransaction()
    {
        // get current count
        $result = \Remix\DJ::play('SELECT * FROM users;');
        $count = count($result);

        $back2back = \Remix\DJ::back2back();
        $this->assertTrue((bool)$back2back);
        $this->assertTrue($back2back instanceof \Remix\DJ\Back2back);

        // rollback transaction
        $back2back->start();
        \Remix\DJ::play('INSERT INTO users(name) VALUES(:name);', ['name' => 'Vader']);
        $back2back->fail();

        // get new count
        $result = \Remix\DJ::play('SELECT * FROM users;');
        $this->assertSame($count, count($result));

        // commit transaction
        $back2back->start();
        \Remix\DJ::play('INSERT INTO users(name) VALUES(:name);', ['name' => 'Leia']);
        $back2back->success();

        // get new count
        $result = \Remix\DJ::play('SELECT * FROM users;');
        $this->assertSame($count + 1, count($result));
    }
}

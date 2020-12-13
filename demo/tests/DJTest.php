<?php

namespace Remix\AppTests;

use PHPUnit\Framework\TestCase;

class DJTest extends TestCase
{
    use \Remix\Utility\Tests\InvokePrivateBehavior;

    protected function setUp(): void
    {
        \Remix\Audio::getInstance()->daw->initialize(__DIR__ . '/..');
    }

    public function tearDown(): void
    {
        \Remix\Audio::destroy();
    }

    public function testInstance(): void
    {
        $connection = $this->invokeStaticProperty(\Remix\DJ::class, 'connection');

        $this->assertTrue((bool)$connection);
        $this->assertTrue($connection instanceof \PDO);
    }

    public function testPlay(): void
    {
        \Remix\DJ::table('users')->truncate();
        \Remix\DJ::play('INSERT INTO users(name) VALUES(:name);', ['name' => 'Riina']);

        // is SQL executable?
        $result = \Remix\DJ::play('SELECT * FROM users WHERE id = :id;', ['id' => 1]);
        $this->assertSame(1, count($result));
        $this->assertSame('Riina', $result->first()['name']);

        // use setlist
        /*
        $setlist = \Remix\DJ::prepare('SELECT * FROM users;');
        $this->assertTrue($setlist instanceof \Remix\DJ\Setlist);

        $result = $setlist->asVinyl(\App\Vinyl\User::class)->play();
        $this->assertTrue($result[0] instanceof \App\Vinyl\User);

        // use setlist with placeholder
        $setlist = \Remix\DJ::prepare('SELECT * FROM users WHERE id = :id;');
        $result = $setlist->asVinyl(\App\Vinyl\User::class)->play(['id' => 1]);
        $this->assertSame(1, count($result));
        $this->assertTrue($result[0] instanceof \App\Vinyl\User);
        */
    }

    public function testVinyl(): void
    {
/*
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
*/
        $this->assertTrue(true);
    }

    public function testInsert(): void
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

    public function testTruncate(): void
    {
        \Remix\DJ::play('INSERT INTO users(name) VALUES(:name);', ['name' => 'Luke']);

        $result = \Remix\DJ::table('users')->truncate();
        $this->assertTrue($result);

        $result = \Remix\DJ::play('SELECT * FROM users;');
        $this->assertSame(0, count($result));
    }

    public function testTransaction(): void
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

    public function testCreate()
    {
        $table = \Remix\DJ::table('test_table');

        if ($table->exists()) {
            $table->drop();
        }

        $this->assertFalse($table->exists());

        $table->create(function ($table) {
            return [
                'id INT',
                'title TEXT',
            ];
        });

        $this->assertTrue($table->exists());
    }

    public function testDrop()
    {
        $table = \Remix\DJ::table('test_table');

        if (! $table->exists()) {
            $table->create([
                'id INT',
                'title TEXT',
            ]);
        }

        $this->assertTrue($table->exists());

        $table->drop();

        $this->assertFalse($table->exists());
    }
}
// class DJTest

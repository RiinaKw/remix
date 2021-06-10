<?php

namespace Remix\AppTests;

use PHPUnit\Framework\TestCase;

class DJTest extends TestCase
{
    use \Utility\Tests\InvokePrivateBehavior;

    protected function setUp(): void
    {
        \Remix\Audio::getInstance()->daw->initialize(__DIR__ . '/../app');
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

    public function testBack2back()
    {
        $back2back = \Remix\DJ::back2back();

        // ** The original behavior is that an exception is thrown
        // ** if beginTransaction is performed before the transaction is completed.
        try {
            // ** Remix doesn't have that problem.
            $back2back->start();
            $back2back->start();
        } catch (\Exception $e) {
            $this->fail('exception throwed');
        }
        $this->assertTrue(true);
    }
}
// class DJTest

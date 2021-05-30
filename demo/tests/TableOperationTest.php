<?php

namespace Remix\AppTests;

use PHPUnit\Framework\TestCase;

class TableOperationTest extends TestCase
{
    use \Remix\Utility\Tests\InvokePrivateBehavior;

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
}
// class DJTest

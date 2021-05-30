<?php

namespace Remix\AppTests;

use PHPUnit\Framework\TestCase;
use Remix\DJ;

class TableOperationTest extends TestCase
{
    protected function setUp(): void
    {
        \Remix\Audio::getInstance()->daw->initialize(__DIR__ . '/../app');
    }

    public function tearDown(): void
    {
        \Remix\Audio::destroy();
    }

    public function testInsert(): void
    {
        // get current count
        $result = DJ::play('SELECT * FROM users;');
        $count = count($result);

        // insert
        $result = DJ::play('INSERT INTO users(name) VALUES(:name);', ['name' => 'Luke']);

        // get new count
        $result = DJ::play('SELECT * FROM users;');
        $this->assertSame($count + 1, count($result));
    }
}
// class TableOperationTest

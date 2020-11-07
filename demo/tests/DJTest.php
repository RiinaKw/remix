<?php

namespace Remix\AppTests;

use PHPUnit\Framework\TestCase;

class DJTest extends TestCase
{
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
        $this->assertTrue((bool)\Remix\DJ::$connection);
        $this->assertTrue(\Remix\DJ::$connection instanceof \PDO);
    }

    public function testPlay()
    {
        // is SQL executable?
        $result = \Remix\DJ::play('SELECT * FROM users;');
        $this->assertSame(2, count($result));

        // is SQL executable with placeholder?
        $result = \Remix\DJ::play('SELECT * FROM users WHERE id = :id;', ['id' => 1]);
        $this->assertSame(1, count($result));
        $this->assertSame('Riina', $result[0]['name']);
    }
}

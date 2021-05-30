<?php

namespace Remix\AppTests;

use PHPUnit\Framework\TestCase;
use Remix\DJ;
use Remix\Exceptions\DJException;

class TableTest extends TestCase
{
    private $table = null;
    private const TABLE_NAME = 'test_table';

    protected function setUp(): void
    {
        \Remix\Audio::getInstance()->initialize()->daw->initialize(__DIR__ . '/../app');
        $this->table = DJ::table(self::TABLE_NAME);
        if ($this->table->exists()) {
            $this->table->drop();
        }
        $this->table->create(function ($table) {
            $table->int('col_pk')->pk();
            $table->varchar('col_uq', 10)->uq();
            $table->timestamp('col_idx')->idx();
            $table->text('col_non_idx');
        });
    }

    public function tearDown(): void
    {
        if ($this->table->exists()) {
            $this->table->drop();
        }
        \Remix\Audio::destroy();
    }

    public function testCreate()
    {
        if ($this->table->exists()) {
            $this->table->drop();
        }
        $this->assertFalse($this->table->exists());

        $this->table->create(function ($table) {
            $table->int('id')->pk();
            $table->text('title');
        });

        $this->assertTrue($this->table->exists());
    }

    public function testDrop()
    {
        if (! $this->table->exists()) {
            $this->table->create(function ($table) {
                $table->int('id');
                $table->text('title');
            });
        }

        $this->assertTrue($this->table->exists());

        $this->table->drop();

        $this->assertFalse($this->table->exists());
    }

    public function testTruncate(): void
    {
        \Remix\DJ::play('INSERT INTO users(name) VALUES(:name);', ['name' => 'Luke']);

        $result = \Remix\DJ::table('users')->truncate();
        $this->assertTrue($result);

        $result = \Remix\DJ::play('SELECT * FROM users;');
        $this->assertSame(0, count($result));
    }
}
// class DJTest

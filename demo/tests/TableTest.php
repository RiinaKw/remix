<?php

namespace Remix\AppTests;

use PHPUnit\Framework\TestCase;
use Remix\DJ;
use Remix\Exceptions\DJException;

class TableTest extends TestCase
{
    private $table = null;

    protected function setUp(): void
    {
        \Remix\Audio::getInstance()->initialize()->daw->initialize(__DIR__ . '/../app');
        $this->table = DJ::table('test_table');
        if ($this->table->exists()) {
            $this->table->drop();
        }
        $this->table->create(function ($table) {
            $table->int('id');
            $table->text('title');
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
        $table_name = $this->table->name;
        $sql = <<<SQL
INSERT INTO `$table_name`(`id`, `title`) VALUES(:id, :title);
SQL;
        \Remix\DJ::play($sql, ['id' => 1, 'title' => 'Luke']);

        $result = \Remix\DJ::play("SELECT * FROM `{$table_name}`;");
        $this->assertSame(1, count($result));

        \Remix\DJ::table($table_name)->truncate();

        $result = \Remix\DJ::play("SELECT * FROM `{$table_name}`;");
        $this->assertSame(0, count($result));
    }

    public function testCreateWithNoColumns()
    {
        $this->expectException(DJException::class);
        $this->expectExceptionMessage($this->table->name);
        $this->expectExceptionMessage('must contains any column');

        if ($this->table->exists()) {
            $this->table->drop();
        }

        $this->table->create(function () {
        });
    }

    public function testCreateDuplicates()
    {
        $this->expectException(DJException::class);
        $this->expectExceptionMessage($this->table->name);
        $this->expectExceptionMessage('already exists');

        if ($this->table->exists()) {
            $this->table->drop();
        }

        $this->table->create(function ($table) {
            $table->int('id');
            $table->text('title');
        });
        $this->table->create(function ($table) {
            $table->int('id');
            $table->text('title');
        });
    }

    public function testDropFail()
    {
        $this->expectException(DJException::class);
        $this->expectExceptionMessage($this->table->name);
        $this->expectExceptionMessage('is not exists');

        if ($this->table->exists()) {
            $this->table->drop();
        }

        $this->table->drop();
    }
}
// class DJTest

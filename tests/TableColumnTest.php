<?php

namespace Remix\CoreTests;

use PHPUnit\Framework\TestCase;
use Remix\DJ\Column;

class TableColumnTest extends TestCase
{
    use \Utility\Tests\InvokePrivateBehavior;

    public function testInt(): void
    {
        $column = Column::factory('id', ['type' => 'INT']);
        $this->assertSame('`id` INT NOT NULL', (string)$column);

        $column = Column::factory('blog_id', ['type' => 'INT'])->nullable()->default(null);
        $this->assertSame('`blog_id` INT NULL DEFAULT NULL', (string)$column);

        $column = Column::factory('flag', ['type' => 'INT', 'length' => 1])->default(0);
        $this->assertSame('`flag` INT(1) NOT NULL DEFAULT 0', (string)$column);

        $column = Column::factory('pk', ['type' => 'INT'])->unsigned()->autoIncrement();
        $this->assertSame('`pk` INT UNSIGNED NOT NULL AUTO_INCREMENT', (string)$column);
    }

    public function testVarchar(): void
    {
        $column = Column::factory('title', ['type' => 'VARCHAR', 'length' => 50])->default('untitled');
        $this->assertSame('`title` VARCHAR(50) NOT NULL DEFAULT \'untitled\'', (string)$column);

        $column = Column::factory('subtitle', ['type' => 'VARCHAR', 'length' => 100])->nullable();
        $this->assertSame('`subtitle` VARCHAR(100) NULL', (string)$column);
    }

    public function testText(): void
    {
        $column = Column::factory('body', ['type' => 'TEXT']);
        $this->assertSame('`body` TEXT NOT NULL', (string)$column);

        $column = Column::factory('append', ['type' => 'TEXT'])->nullable();
        $this->assertSame('`append` TEXT NULL', (string)$column);
    }

    public function testDatetime(): void
    {
        $column = Column::factory('created_at', ['type' => 'DATETIME'])->default('0000-00-00 00:00:00');
        $this->assertSame('`created_at` DATETIME NOT NULL DEFAULT \'0000-00-00 00:00:00\'', (string)$column);

        $column = Column::factory('deleted_at', ['type' => 'DATETIME'])->nullable();
        $this->assertSame('`deleted_at` DATETIME NULL', (string)$column);
    }

    public function testTimestamp(): void
    {
        $column = Column::factory('updated_at', ['type' => 'TIMESTAMP'])->currentTimestamp();
        $this->assertSame('`updated_at` TIMESTAMP NOT NULL DEFAULT current_timestamp()', (string)$column);

        $column = Column::factory('logined_at', ['type' => 'TIMESTAMP'])->nullable();
        $this->assertSame('`logined_at` TIMESTAMP NULL', (string)$column);
    }
}
// class TrackTest

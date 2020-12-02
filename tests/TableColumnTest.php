<?php

namespace Remix\CoreTests;

use PHPUnit\Framework\TestCase;
use Remix\DJ\Column;

class TableColumnTest extends TestCase
{
    use \Remix\Utility\Tests\InvokePrivateBehavior;

    public function testInt(): void
    {
        $column = new Column('id', 'INT');
        $this->assertSame('`id` INT NOT NULL', (string)$column);

        $column = new Column('blog_id', 'INT');
        $column->nullable();
        $column->default(null);
        $this->assertSame('`blog_id` INT NULL DEFAULT NULL', (string)$column);

        $column = new Column('flag', 'INT', 1);
        $column->default(0);
        $this->assertSame('`flag` INT(1) NOT NULL DEFAULT 0', (string)$column);

        $column = new Column('pk', 'INT');
        $column->unsigned();
        $column->autoIncrement();
        $this->assertSame('`pk` INT UNSIGNED NOT NULL AUTO_INCREMENT', (string)$column);
    }

    public function testVarchar(): void
    {
        $column = new Column('title', 'VARCHAR', 50);
        $column->default('untitled');
        $this->assertSame('`title` VARCHAR(50) NOT NULL DEFAULT \'untitled\'', (string)$column);

        $column = new Column('subtitle', 'VARCHAR', 100);
        $column->nullable();
        $this->assertSame('`subtitle` VARCHAR(100) NULL', (string)$column);
    }

    public function testText(): void
    {
        $column = new Column('body', 'TEXT');
        $this->assertSame('`body` TEXT NOT NULL', (string)$column);

        $column = new Column('append', 'TEXT');
        $column->nullable();
        $this->assertSame('`append` TEXT NULL', (string)$column);
    }

    public function testDatetime(): void
    {
        $column = new Column('created_at', 'DATETIME');
        $column->default('0000-00-00 00:00:00');
        $this->assertSame('`created_at` DATETIME NOT NULL DEFAULT \'0000-00-00 00:00:00\'', (string)$column);

        $column = new Column('deleted_at', 'DATETIME');
        $column->nullable();
        $this->assertSame('`deleted_at` DATETIME NULL', (string)$column);
    }

    public function testTimestamp(): void
    {
        $column = new Column('updated_at', 'TIMESTAMP');
        $column->currentTimestamp();
        $this->assertSame('`updated_at` TIMESTAMP NOT NULL DEFAULT current_timestamp()', (string)$column);

        $column = new Column('logined_at', 'TIMESTAMP');
        $column->nullable();
        $this->assertSame('`logined_at` TIMESTAMP NULL', (string)$column);
    }
}
// class TrackTest

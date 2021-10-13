<?php

namespace Remix\CoreTests;

use PHPUnit\Framework\TestCase;
use Remix\DJ\Column;
use Remix\DJ\Columns;

class TableColumnTest extends TestCase
{
    use \Utility\Tests\InvokePrivateBehavior;

    public function testInt(): void
    {
        $column = (new Columns\IntCol('id'));
        $this->assertSame('`id` INT(11) NOT NULL', (string)$column);

        $column = (new Columns\IntCol('blog_id'))->nullable()->default(null);
        $this->assertSame('`blog_id` INT(11) NULL DEFAULT NULL', (string)$column);

        $column = (new Columns\IntCol('flag', 11))->default(0);
        $this->assertSame('`flag` INT(11) NOT NULL DEFAULT 0', (string)$column);

        $column = (new Columns\IntCol('pk'))->unsigned()->autoIncrement();
        $this->assertSame('`pk` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT', (string)$column);
    }

    public function testVarchar(): void
    {
        $column = (new Columns\VarcharCol('title', 50))->default('untitled');
        $this->assertSame('`title` VARCHAR(50) NOT NULL DEFAULT \'untitled\'', (string)$column);

        $column = (new Columns\VarcharCol('subtitle', 100))->nullable();
        $this->assertSame('`subtitle` VARCHAR(100) NULL', (string)$column);
    }

    public function testText(): void
    {
        $column = (new Columns\TextCol('body'));
        $this->assertSame('`body` TEXT NOT NULL', (string)$column);

        $column = (new Columns\TextCol('append'))->nullable();
        $this->assertSame('`append` TEXT NULL', (string)$column);
    }

    public function testDatetime(): void
    {
        $column = (new Columns\DatetimeCol('created_at'))->default('0000-00-00 00:00:00');
        $this->assertSame('`created_at` DATETIME NOT NULL DEFAULT \'0000-00-00 00:00:00\'', (string)$column);

        $column = (new Columns\DatetimeCol('deleted_at'))->nullable();
        $this->assertSame('`deleted_at` DATETIME NULL', (string)$column);
    }

    public function testTimestamp(): void
    {
        $column = (new Columns\TimestampCol('updated_at'))->currentTimestamp();
        $this->assertSame('`updated_at` TIMESTAMP NOT NULL DEFAULT current_timestamp()', (string)$column);

        $column = (new Columns\TimestampCol('logined_at'))->nullable();
        $this->assertSame('`logined_at` TIMESTAMP NULL', (string)$column);
    }
}
// class TrackTest

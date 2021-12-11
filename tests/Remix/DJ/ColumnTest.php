<?php

namespace Remix\CoreTests;

use Utility\Tests\BaseTestCase as TestCase;
// Target of the test
use Remix\DJ\Columns;

class TableColumnTest extends TestCase
{
    public function testInt(): void
    {
        $column = (new Columns\IntCol('id'));
        $this->assertSame('INT(11) NOT NULL', (string)$column);

        $column = (new Columns\IntCol('blog_id'))->nullable()->default(null);
        $this->assertSame('INT(11) NULL DEFAULT NULL', (string)$column);

        $column = (new Columns\IntCol('flag', 11))->default(0);
        $this->assertSame('INT(11) NOT NULL DEFAULT 0', (string)$column);

        $column = (new Columns\IntCol('pk'))->unsigned()->autoIncrement();
        $this->assertSame('INT(11) UNSIGNED NOT NULL AUTO_INCREMENT', (string)$column);
    }

    public function testVarchar(): void
    {
        $column = (new Columns\VarcharCol('title', 50))->default('untitled');
        $this->assertSame('VARCHAR(50) NOT NULL DEFAULT \'untitled\'', (string)$column);

        $column = (new Columns\VarcharCol('subtitle', 100))->nullable();
        $this->assertSame('VARCHAR(100) NULL', (string)$column);
    }

    public function testText(): void
    {
        $column = (new Columns\TextCol('body'));
        $this->assertSame('TEXT NOT NULL', (string)$column);

        $column = (new Columns\TextCol('append'))->nullable();
        $this->assertSame('TEXT NULL', (string)$column);
    }

    public function testDatetime(): void
    {
        $column = (new Columns\DatetimeCol('created_at'))->default('0000-00-00 00:00:00');
        $this->assertSame('DATETIME NOT NULL DEFAULT \'0000-00-00 00:00:00\'', (string)$column);

        $column = (new Columns\DatetimeCol('deleted_at'))->nullable();
        $this->assertSame('DATETIME NULL', (string)$column);
    }

    public function testTimestamp(): void
    {
        $column = (new Columns\TimestampCol('updated_at'))->currentTimestamp();
        $this->assertSame('TIMESTAMP NOT NULL DEFAULT current_timestamp()', (string)$column);

        $column = (new Columns\TimestampCol('logined_at'))->nullable();
        $this->assertSame('TIMESTAMP NULL', (string)$column);
    }
}
// class TrackTest

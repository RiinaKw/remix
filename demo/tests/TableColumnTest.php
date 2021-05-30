<?php

namespace Remix\AppTests;

use PHPUnit\Framework\TestCase;
use Remix\DJ;
use Remix\DJ\Column;

class TableColumnTest extends TestCase
{
    private $db = null;
    private $table = null;

    protected function setUp(): void
    {
        \Remix\Audio::getInstance()->initialize()->daw->initialize(__DIR__ . '/../app');
        $this->table = DJ::table('test_table');
        if ($this->table->exists()) {
            $this->table->drop();
        }
        $this->table->create(function ($table) {
            $table->int('col_pk')->pk();
            $table->varchar('col_uq', 10)->uq();
            $table->timestamp('col_idx')->idx();
            $table->text('col_non_idx');
        });

        $dsn = \Remix\Audio::getInstance()->preset->get('app.db.dsn');
        preg_match('/dbname=(?<db>.*)$/', $dsn, $matches);
        $this->db = $matches['db'];
    }

    public function tearDown(): void
    {
        $this->table->drop();
        \Remix\Audio::destroy();
    }

    public function testInstance()
    {
        $column = $this->table->column('col_pk');
        $this->assertTrue($column instanceof Column);
        $this->assertSame('col_pk', $column->name);
    }

    public function testIndex()
    {
        $table_name = $this->table->name;
        $sql = <<<SQL
SELECT TABLE_NAME, INDEX_NAME, COLUMN_NAME FROM information_schema.STATISTICS
WHERE TABLE_SCHEMA = '{$this->db}'
    AND TABLE_NAME = '{$table_name}'
    AND COLUMN_NAME = :column;
SQL;

        $arr = [
            'col_pk' => 'pk',
            'col_uq' => 'uq',
            'col_idx' => 'idx',
        ];
        foreach ($arr as $column_name => $index_type) {
            $setlist = DJ::play($sql, [ ':column' => $column_name ]);
            $this->assertSame(1, $setlist->count());
            $column = $this->table->column($column_name);
            $this->assertSame($index_type, $column->index);
            $row = $setlist->first();
            $this->assertTrue((bool)$row);
        }

        $setlist = DJ::play($sql, [ ':column' => 'col_non_idx' ]);
        $this->assertSame(0, $setlist->count());
    }
}

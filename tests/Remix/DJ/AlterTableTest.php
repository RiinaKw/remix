<?php

namespace Remix\CoreTests;

use PHPUnit\Framework\TestCase;
use Remix\Instruments\DJ;
use Remix\DJ\MC;
use Remix\DJ\Table;
use Remix\DJ\Column;
use Remix\DJ\Index;
use Remix\DJ\Columns;
use Remix\Exceptions\DJException;

class AlterTableTest extends TestCase
{
    protected function setUp(): void
    {
        \Remix\Audio::getInstance()->preset->set('app', require('TestEnv.php'));
    }

    public function tearDown(): void
    {
        \Remix\Audio::destroy();
    }

    protected function prepareTable(): void
    {
        MC::tableDropForce('test');

        $table = DJ::table('test');
        $table->create(function (Table $table) {
            $table->comment('sample table');
            Column::int('id')->unsigned()->comment('sample')
                ->pk()->append($table);
            Column::varchar('user_id', 100)->nullable()->default(0)->comment('of')
                ->uq()->append($table);
            Column::timestamp('created_at')->currentTimestamp()->comment('comment')
                ->idx()->append($table);
        });
    }

    public function testAddColumn(): void
    {
        $this->prepareTable();

        // Now there are 3 columns
        $columns = MC::tableColumns('test');
        $this->assertSame(['id', 'user_id', 'created_at'], array_keys($columns));

        // Add a column
        $table = DJ::table('test');
        $table->modify(function (Table $table) {
            Column::text('description')->append($table)->add();
        });

        // There should be 4 columns
        $columns = MC::tableColumns('test');
        $this->assertSame(['id', 'user_id', 'created_at', 'description'], array_keys($columns));
    }

    public function testAddColumnWithOrder(): void
    {
        $this->prepareTable();

        // Add a column after id
        $table = DJ::table('test');
        $table->modify(function (Table $table) {
            Column::varchar('name', 100)->append($table)->add()->after('id');
        });

        // Has order changed?
        $columns = MC::tableColumns('test');
        $this->assertSame(['id', 'name', 'user_id', 'created_at'], array_keys($columns));
    }

    public function testModifyColumn(): void
    {
        $this->prepareTable();

        // Add a column after id
        $table = DJ::table('test');
        $table->modify(function (Table $table) {
            Column::varchar('user_name', 200)->append($table)->replace('user_id');
        });

        // Has order changed?
        $this->assertNull(MC::tableColumns($table, 'user_id'));

        $columns = MC::tableColumns($table);
        $this->assertSame(['id', 'user_name', 'created_at'], array_keys($columns));

        $column = MC::tableColumns($table, 'user_name');
        $this->assertSame('user_name', $column->name);
        $this->assertSame('VARCHAR', $column->type);
        $this->assertSame(200, $column->length);
    }

    public function testModifyColumnDuplicate(): void
    {
        $this->expectException(DJException::class);
        $this->expectExceptionMessage("Column `id` is already exists in `test`");

        MC::tableDropForce('test');

        // Created without problems
        $table = DJ::table('test');
        $table->create(function (Table $table) {
            Column::int('id')->append($table);
        });

        // Try to add a column that already exists
        $table->modify(function (Table $table) {
            Column::int('id')->append($table);
        });
    }

    public function testRenameColumn(): void
    {
        $this->prepareTable();

        $table = DJ::table('test');
        $table->modify(function (Table $table) {
            $table->renameColumn('created_at', 'modified_at');
        });

        // Has order changed?
        $this->assertNull(MC::tableColumns($table, 'created_at'));

        $column = MC::tableColumns($table, 'modified_at');
        $this->assertSame('modified_at', $column->name);

        $columns = MC::tableColumns($table);
        $this->assertSame(['id', 'user_id', 'modified_at'], array_keys($columns));
    }

    public function testDropColumn(): void
    {
        $this->prepareTable();

        // Add a column after id
        $table = DJ::table('test');
        $table->modify(function (Table $table) {
            $table->dropColumn('user_id');
        });

        // Has order changed?
        $this->assertNull(MC::tableColumns($table, 'user_id'));

        $columns = MC::tableColumns($table);
        $this->assertSame(['id', 'created_at'], array_keys($columns));
    }

    public function testModifyMixed(): void
    {
        $this->prepareTable();

        // Add a column after id
        $table = DJ::table('test');
        $table->modify(function (Table $table) {
            // replace
            Column::varchar('company', 50)->append($table)->replace('id');

            // add
            Column::varchar('name', 100)->append($table)->add()->after('company');

            // drop
            $table->dropColumn('user_id');

            // rename
            $table->renameColumn('created_at', 'modified_at');
        });

        $columns = MC::tableColumns($table);
        $this->assertSame(['company', 'name', 'modified_at'], array_keys($columns));
    }
}

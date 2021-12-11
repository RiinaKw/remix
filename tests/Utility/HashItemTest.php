<?php

namespace Remix\UtilityTests;

use Utility\Tests\BaseTestCase as TestCase;
// Target of the test
use Utility\Hash;
// Exception
use Exception;

class HashItemTest extends TestCase
{
    protected $readonlyhash = null;

    protected function setUp(): void
    {
        $this->readonlyhash = new Hash\ReadOnlyHash([
            'key1' => [
                'subkey1' => 1,
                'subkey2' => 'foo',
            ]
        ]);
    }

    public function testGet(): void
    {
        $item = $this->readonlyhash->item('key1.subkey2');
        $this->assertSame('foo', $item->get());
    }

    public function testGetDefault(): void
    {
        $item = $this->readonlyhash->item('key1.notexists');
        $this->assertSame(null, $item->get());
        $this->assertSame('default', $item->get('default'));
    }

    public function testSetForbidden(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Hash item 'key1.subkey2' is not editable");

        $item = $this->readonlyhash->item('key1.subkey2');
        $item->set('bar');
    }

    public function testDeleteForbidden(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Hash item 'key1.subkey2' is not editable");

        $item = $this->readonlyhash->item('key1.subkey2');
        $item->delete();
    }
}

<?php

namespace Remix\UtilityTests;

use PHPUnit\Framework\TestCase;
// Target of the test
use Utility\Hash;

class HashItemEditableTest extends TestCase
{
    protected $hash = null;

    protected function setUp(): void
    {
        $this->hash = new Hash([
            'key1' => [
                'subkey1' => 1,
                'subkey2' => 'foo',
            ]
        ]);
    }

    public function testGet(): void
    {
        $item = $this->hash->item('key1.subkey2');
        $this->assertSame('foo', $item->get());
    }

    public function testGetDefault(): void
    {
        $item = $this->hash->item('key1.notexists');
        $this->assertSame(null, $item->get());
        $this->assertSame('default', $item->get('default'));
    }

    public function testSet(): void
    {
        $item = $this->hash->item('key1.subkey2');

        $this->assertSame('foo', $this->hash->get('key1.subkey2'));

        $item->set('bar');
        $this->assertSame('bar', $this->hash->get('key1.subkey2'));
    }

    public function testDelete(): void
    {
        $item = $this->hash->item('key1.subkey2');

        $this->assertTrue($this->hash->isSet('key1.subkey2'));

        $item->delete();
        $this->assertFalse($this->hash->isSet('key1.subkey2'));
    }
}

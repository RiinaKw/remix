<?php

namespace Remix\UtilityTests;

use PHPUnit\Framework\TestCase;
use Utility\Hash;

class HashTest extends TestCase
{
    use \Utility\Tests\InvokePrivateBehavior;

    public function testInstance(): void
    {
        $hash = new Hash();
        $this->assertNotNull($hash);

        // is properly initialized?
        $this->assertSame([], $this->invokeProperty($hash, 'source'));

        // is properly initialized with default array?
        $initial = [
            'foo' => 'bar',
        ];
        $hash = new Hash($initial);
        $this->assertSame($initial, $this->invokeProperty($hash, 'source'));
    }

    public function testIsset(): void
    {
        $hash = new Hash([
            'foo' => 'bar',
            'nest' => [
                'depth-1' => [
                    'depth-2' => 'ahhhh',
                ]
            ],
        ]);

        // is isset-able?
        $this->assertTrue($hash->isset('foo'));

        // is isset-able with the magic method?
        $this->assertTrue(isset($hash->foo));

        // is isset-able with a deep nest?
        $this->assertTrue($hash->isset('nest.depth-1.depth-2'));

        // is isset-able?
        $this->assertFalse($hash->isset('noexists'));

        // is isset-able with the magic method?
        $this->assertFalse(isset($hash->noexists));
    }

    public function testGet(): void
    {
        $hash = new Hash([
            'foo' => 'bar',
            'nest' => [
                'depth-1' => [
                    'depth-2' => 'ahhhh',
                ]
            ],
        ]);

        // is getable?
        $this->assertSame('bar', $hash->get('foo'));

        // is getable with the magic method?
        $this->assertSame('bar', $hash->foo);

        // is getable with a deep nest?
        $this->assertSame(['depth-2' => 'ahhhh'], $hash->get('nest.depth-1'));
        $this->assertSame('ahhhh', $hash->get('nest.depth-1.depth-2'));

        // will NOT be overwritten?
        $array = $hash->get('nest.depth-1');
        $array['depth-2'] = 'booo';
        $this->assertSame(['depth-2' => 'ahhhh'], $hash->get('nest.depth-1'));

        // will a non-existent key be null?
        $this->assertNull($hash->get('no-exists'));

        // will a non-existent key with deep-nested be null?
        $this->assertNull($hash->get('no-exists.something'));
    }

    public function testSet(): void
    {
        $hash = new Hash([
            'foo' => 'bar',
        ]);

        $hash->set('boo', 1);

        // is settable?
        $this->assertSame(1, $hash->get('boo'));

        // is settable with the magic method?
        $this->assertSame(1, $hash->boo);

        // is settable with a deep nest?
        $hash->set('nest.depth-1.depth-2', 'ahhhh');
        $this->assertSame(['depth-2' => 'ahhhh'], $hash->get('nest.depth-1'));

        // is overrwitable?
        $this->assertSame('bar', $hash->foo);
        $hash->foo = 'baaaa';
        $this->assertSame('baaaa', $hash->foo);
    }

    public function testDelete(): void
    {
        $hash = new Hash([
            'foo' => 'bar',
        ]);

        // is deletable?
        $this->assertSame(['foo' => 'bar'], $this->invokeProperty($hash, 'source'));
        $hash->delete('foo');
        $this->assertSame([], $this->invokeProperty($hash, 'source'));

        // undo once
        $hash->foo = 'another bar';
        $this->assertSame('another bar', $hash->foo);

        // is deletable with the magic method?
        unset($hash->foo);
        $this->assertNull($hash->foo);
        $this->assertSame([], $this->invokeProperty($hash, 'source'));

        // no any errors by deleting the non-existent key?
        unset($hash->noexists);
        $this->assertNull($hash->noexists);
    }

    public function testTruncate(): void
    {
        $hash = new Hash([
            'foo' => 'bar',
        ]);

        // is deletable?
        $this->assertSame(['foo' => 'bar'], $this->invokeProperty($hash, 'source'));
        $hash->truncate();
        $this->assertSame([], $this->invokeProperty($hash, 'source'));
    }

    public function testRef(): void
    {
        $array = [
            'foo' => 'bar',
        ];
        $hash = new Hash();
        $hash->ref($array);

        // via a variable to the hash
        $array['foo'] = 'aaa';
        $this->assertSame(['foo' => 'aaa'], $this->invokeProperty($hash, 'source'));

        // via a hash to the variable
        $hash->foo = 'zzz';
        $this->assertSame(['foo' => 'zzz'], $array);
    }
}
// class AmpTest

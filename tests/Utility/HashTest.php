<?php

namespace Remix\UtilityTests;

use PHPUnit\Framework\TestCase;
// Target of the test
use Utility\Hash;
// Utility
use Utility\Reflection\ReflectionObject;

class HashTest extends TestCase
{
    public function testInstance(): void
    {
        // Set up the Hash
        $hash = new Hash();

        // Set up the Reflection
        $reflection = new ReflectionObject($hash);

        // Is properly initialized?
        $this->assertSame([], $reflection->getProp('source'));
    }

    public function testInstanceWithInitialize()
    {
        // Is properly initialized with default array?
        $initial = [
            'foo' => 'bar',
        ];
        $hash = new Hash($initial);

        // Set up the Reflection
        $reflection = new ReflectionObject($hash);

        // Does it have the same property?
        $this->assertSame($initial, $reflection->getProp('source'));
    }

    public function testIsset(): void
    {
        // Set up the Hash
        $hash = new Hash([
            'foo' => 'bar',
            'nest' => [
                'depth-1' => [
                    'depth-2' => 'ahhhh',
                ]
            ],
        ]);

        // Is isset-able?
        $this->assertTrue($hash->isset('foo'));

        // Is isset-able with the magic method?
        $this->assertTrue(isset($hash->foo));

        // Is isset-able with a deep nest?
        $this->assertTrue($hash->isset('nest.depth-1.depth-2'));

        // Is isset-able?
        $this->assertFalse($hash->isset('noexists'));

        // Is isset-able with the magic method?
        $this->assertFalse(isset($hash->noexists));
    }

    public function testGet(): void
    {
        // Set up the Hash
        $hash = new Hash([
            'foo' => 'bar',
            'nest' => [
                'depth-1' => [
                    'depth-2' => 'ahhhh',
                ]
            ],
        ]);

        // Is getable?
        $this->assertSame('bar', $hash->get('foo'));

        // Is getable with the magic method?
        $this->assertSame('bar', $hash->foo);

        // Is getable with a deep nest?
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
        // Set up the Hash
        $hash = new Hash([
            'foo' => 'bar',
        ]);

        // Try to set
        $hash->set('boo', 1);

        // Is settable?
        $this->assertSame(1, $hash->get('boo'));

        // Is settable with the magic method?
        $this->assertSame(1, $hash->boo);

        // Is settable with a deep nest?
        $hash->set('nest.depth-1.depth-2', 'ahhhh');
        $this->assertSame(['depth-2' => 'ahhhh'], $hash->get('nest.depth-1'));

        // Is overrwitable?
        $this->assertSame('bar', $hash->foo);
        $hash->foo = 'baaaa';
        $this->assertSame('baaaa', $hash->foo);
    }

    public function testDelete(): void
    {
        // Set up the Hash
        $hash = new Hash([
            'foo' => 'bar',
        ]);

        // Set up the Reflection
        $reflection = new ReflectionObject($hash);

        // Is deletable?
        $this->assertSame(['foo' => 'bar'], $reflection->getProp('source'));
        $hash->delete('foo');
        $this->assertNull($hash->get('foo'));
        $this->assertSame([], $reflection->getProp('source'));

        // Reset with another value
        $hash->foo = 'another bar';
        $this->assertSame('another bar', $hash->foo);

        // Is deletable with the magic method?
        unset($hash->foo);
        $this->assertNull($hash->foo);
        $this->assertSame([], $reflection->getProp('source'));

        // No any errors by deleting the non-existent key?
        unset($hash->noexists);
        $this->assertNull($hash->noexists);
    }

    public function testTruncate(): void
    {
        // Set up the Hash
        $hash = new Hash([
            'foo' => 'bar',
        ]);

        // Set up the Reflection
        $reflection = new ReflectionObject($hash);

        // Confirm that it is the expected variable
        $this->assertSame(['foo' => 'bar'], $reflection->getProp('source'));

        // Is deletable?
        $hash->truncate();
        $this->assertSame([], $reflection->getProp('source'));
    }

    public function testRef(): void
    {
        // Set up the Hash
        $array = [
            'foo' => 'bar',
        ];
        $hash = new Hash();
        $hash->ref($array);

        // Set up the Reflection
        $reflection = new ReflectionObject($hash);

        // Can it edit the hash using a method?
        $array['foo'] = 'aaa';
        $this->assertSame(['foo' => 'aaa'], $reflection->getProp('source'));

        // Can it edit the hash like a property?
        $hash->foo = 'zzz';
        $this->assertSame(['foo' => 'zzz'], $array);
    }
}
// class AmpTest

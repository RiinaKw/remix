<?php

namespace Utility\Tests;

/**
 * Using reflection classes to allow access to private or protected properties/methods
 *
 * @package  Utility\Tests
 * @todo Write the details.
 */
trait InvokePrivateBehavior
{
    protected function invokeMethod(object $obj, string $name, array $param = [])
    {
        $reflection = new \ReflectionClass($obj);
        $method = $reflection->getMethod($name);
        $method->setAccessible(true);
        return $method->invokeArgs($obj, $param);
    }
    // function invokeMethod

    protected function invokeProperty(object $obj, string $name)
    {
        $reflection = new \ReflectionClass($obj);
        $prop = $reflection->getProperty($name);
        $prop->setAccessible(true);
        return $prop->getValue($obj);
    }
    // function invokeProperty

    protected function invokeStaticMethod(string $classname, string $name, array $param = [])
    {
        $reflection = new \ReflectionClass($classname);
        $method = $reflection->getMethod($name);
        $method->setAccessible(true);
        return $method->invokeArgs(null, $param);
    }
    // function invokeStaticMethod

    protected function invokeStaticProperty(string $className, string $name)
    {
        $reflection = new \ReflectionClass($className);
        $props = $reflection->getStaticProperties();
        return $props[$name] ?: null;
    }
    // function invokeStaticProperty
}
// trait InvokePrivateBehavior

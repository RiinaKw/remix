<?php

namespace Remix\Utility\Tests;

trait InvokePrivateBehavior
{
    protected function invokeMethod($obj, string $name, array $param = [])
    {
        $reflection = new \ReflectionClass($obj);
        $method = $reflection->getMethod($name);
        $method->setAccessible(true);
        return $method->invokeArgs($obj, $param);
    } // function invokeMethod

    protected function invokeProperty($obj, string $name)
    {
        $reflection = new \ReflectionClass($obj);
        $prop = $reflection->getProperty($name);
        $prop->setAccessible(true);
        return $prop->getValue($obj);
    } // function invokeMethod

    protected function staticProperty($className, string $name)
    {
        $reflection = new \ReflectionClass($className);
        $props = $reflection->getStaticProperties();
        return $props[$name] ?: null;
    } // function invokeProperty
} // trait InvokePrivateBehavior

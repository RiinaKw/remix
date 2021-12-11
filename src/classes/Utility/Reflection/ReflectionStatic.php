<?php

namespace Utility\Reflection;

use ReflectionClass;
use ReflectionProperty;

/**
 * Reflection for private/protected static members
 *
 * @package  Utility\Reflection
 */
class ReflectionStatic
{
    private $reflection = null;

    public function __construct(string $classname)
    {
        $this->reflection = new ReflectionClass($classname);
    }

    private function getPropReflection(string $name): ReflectionProperty
    {
        $prop = $this->reflection->getProperty($name);
        $prop->setAccessible(true);
        return $prop;
    }

    public function getProp(string $name)
    {
        return $this->getPropReflection($name)->getValue();
    }

    public function setProp(string $name, $value): void
    {
        $this->getPropReflection($name)->setValue($value);
    }

    public function executeMethod(string $name, array $param = [])
    {
        $method = $this->reflection->getMethod($name);
        $method->setAccessible(true);
        return $method->invokeArgs(null, $param);
    }
    // function invokeStaticMethod()
}

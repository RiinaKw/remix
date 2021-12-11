<?php

namespace Utility\Reflection;

use ReflectionClass;
use ReflectionProperty;

/**
 * Reflection for private/protected members
 *
 * @package  Utility\Reflection
 */
class ReflectionObject
{
    private $target = null;
    private $reflection = null;

    public function __construct(object $target)
    {
        $this->target = $target;
        $this->reflection = new ReflectionClass($target);
    }

    private function getPropReflection(string $name): ReflectionProperty
    {
        $prop = $this->reflection->getProperty($name);
        $prop->setAccessible(true);
        return $prop;
    }

    public function getProp(string $name)
    {
        return $this->getPropReflection($name)->getValue($this->target);
    }

    public function setProp(string $name, $value): void
    {
        $this->getPropReflection($name)->setValue($this->target, $value);
    }

    public function executeMethod(string $name, array $params = [])
    {
        $method = $this->reflection->getMethod($name);
        $method->setAccessible(true);
        return $method->invokeArgs($this->target, $params);
    }
}

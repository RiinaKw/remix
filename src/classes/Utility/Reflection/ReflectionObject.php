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
    /**
     * Target object
     * @var object
     */

    private $target = null;

    /**
     * Reflection object
     * @var ReflectionClass
     */
    private $reflection = null;

    /**
     * Set up the ReflectionClass object.
     * @param object $target  target object
     */
    public function __construct(object $target)
    {
        $this->target = $target;
        $this->reflection = new ReflectionClass($target);
    }

    /**
     * Get the private/protected property object.
     * @param  string             $name  property name
     * @return ReflectionProperty        property instance
     */
    private function getPropReflection(string $name): ReflectionProperty
    {
        $prop = $this->reflection->getProperty($name);
        $prop->setAccessible(true);
        return $prop;
    }

    /**
     * Get the value of private/protected property object.
     * @param  string $name  property name
     * @return midex         property value
     */
    public function getProp(string $name)
    {
        return $this->getPropReflection($name)->getValue($this->target);
    }

    /**
     * Set the value of private/protected property object.
     * @param string $name   property name
     * @param mixed  $value  property value
     */
    public function setProp(string $name, $value): void
    {
        $this->getPropReflection($name)->setValue($this->target, $value);
    }

    /**
     * Call the private/protected method.
     * @param  string $name   method name
     * @param  array  $param  arguments of method
     * @return mixed          return value of the method
     */
    public function executeMethod(string $name, array $params = [])
    {
        $method = $this->reflection->getMethod($name);
        $method->setAccessible(true);
        return $method->invokeArgs($this->target, $params);
    }
}

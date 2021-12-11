<?php

namespace Utility\Reflection;

use ReflectionClass;
use ReflectionProperty;

/**
 * Reflection for private/protected static members.
 *
 * @package  Utility\Reflection
 */
class ReflectionStatic
{
    /**
     * Reflection object
     * @var ReflectionClass
     */
    private $reflection = null;

    /**
     * Set up the ReflectionClass object.
     * @param string $classname  target class name
     */
    public function __construct(string $classname)
    {
        $this->reflection = new ReflectionClass($classname);
    }

    /**
     * Get the private/protected static property object.
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
     * Get the value of private/protected static property object.
     * @param  string $name  property name
     * @return midex         property value
     */
    public function getProp(string $name)
    {
        return $this->getPropReflection($name)->getValue();
    }

    /**
     * Set the value of private/protected static property object.
     * @param string $name   property name
     * @param mixed  $value  property value
     */
    public function setProp(string $name, $value): void
    {
        $this->getPropReflection($name)->setValue($value);
    }

    /**
     * Call the private/protected static method.
     * @param  string $name   method name
     * @param  array  $param  arguments of method
     * @return mixed          return value of the method
     */
    public function executeMethod(string $name, array $param = [])
    {
        $method = $this->reflection->getMethod($name);
        $method->setAccessible(true);
        return $method->invokeArgs(null, $param);
    }
    // function invokeStaticMethod()
}

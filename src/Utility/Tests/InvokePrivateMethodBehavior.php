<?php

namespace Remix\Utility\Tests;

trait InvokePrivateMethodBehavior
{
    protected function invokeMethod($obj, string $name, array $param = [])
    {
        $reflection = new \ReflectionClass($obj);
        $method = $reflection->getMethod($name);
        $method->setAccessible(true);
        return $method->invokeArgs($obj, $param);
    }
}

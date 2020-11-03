<?php

namespace Remix\Utility\Tests;

use PHPUnit\Framework\TestCase;

class TestCaseBase extends TestCase
{
    protected function setUp() : void
    {
        require_once(__DIR__ . '/../../../vendor/autoload.php');
    }
}

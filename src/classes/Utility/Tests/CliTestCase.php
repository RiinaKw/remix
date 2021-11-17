<?php

namespace Utility\Tests;

use Utility\Tests\DemoTestCase;

abstract class CliTestCase extends DemoTestCase
{
    public function execute(string $args)
    {
        $this->daw->playCli(explode(' ', $args));
    }
}

<?php

namespace Remix\Demo\TestCase;

abstract class CliTestCase extends DemoTestCase
{
    protected function initialize(string $app_dir)
    {
        $this->daw->initialize($app_dir);
        chdir($app_dir . '/..');
    }

    public function execute(string $args)
    {
        $this->daw->playCli(explode(' ', $args));
    }
}

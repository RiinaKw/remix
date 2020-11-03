<?php

namespace Remix\Utility\Tests;

trait CaptureOutput
{
    protected function startCapture()
    {
        ob_start();
    }

    protected function endCapture()
    {
        return ob_get_clean();
    }

    protected function capture($call, $args)
    {
        $this->startCapture();
        $call($args);
        return $this->endCapture();
    }
}

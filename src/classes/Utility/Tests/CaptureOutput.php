<?php

namespace Utility\Tests;

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

    protected function capture($call, $args = null)
    {
        $this->startCapture();
        $call($args);
        return $this->endCapture();
    }
}

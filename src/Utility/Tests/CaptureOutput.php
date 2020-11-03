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
}

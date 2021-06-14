<?php

namespace Utility\Tests;

/**
 * Utilities of capturing output
 *
 * @package  Utility\Tests
 * @deprecated  A similar function exists separately.
 * @see  Utility\Capture
 */
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

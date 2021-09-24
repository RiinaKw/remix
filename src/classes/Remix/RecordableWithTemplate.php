<?php

namespace Remix;

use Utility\Capture;

/**
 * Recordable using template files
 *
 * @package  Remix\Base
 * @todo Write the details.
 */
trait RecordableWithTemplate
{
    /**
     * @property \Utility\Hash $property
     * @see \Remix\Studio
     */

    protected static $bounce_dir = [];

    protected function pathes()
    {
        if (! static::$bounce_dir) {
            static::$bounce_dir = [
                'remix' => Audio::getInstance()->preset->get('remix.pathes.bounce_dir'),
                'app' => Audio::getInstance()->preset->get('app.pathes.bounce_dir'),
            ];
        }
    }

    protected function template(string $path = null): string
    {
        if (! $path || ! file_exists($path)) {
            foreach (static::$bounce_dir as $dir) {
                $path = $dir . '/' . $this->property->file . '.tpl';
                if (file_exists($path)) {
                    break;
                }
            }
        }
        if (! $path || ! file_exists($path)) {
            throw new RemixException('bounce "' . $this->property->file . '" not found');
        }

        return Capture::capture(function () use ($path) {
            require($path);
        });
    }
    // function template()
}
// trait RecordableWithTemplate

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
    protected function template(string $path = null): string
    {
        if (! $path || ! file_exists($path)) {
            $audio = Audio::getInstance();
            $dirs = [
                $audio->preset->get('app.pathes.bounce_dir'),
                $audio->preset->get('remix.pathes.bounce_dir'),
            ];
            $audio = null;
            foreach ($dirs as $dir) {
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

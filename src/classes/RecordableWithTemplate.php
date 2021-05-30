<?php

namespace Remix;

trait RecordableWithTemplate
{
    protected function template(string $path = null): string
    {
        if (! $path) {
            $audio = Audio::getInstance();
            $dirs = [
                $audio->preset->get('app.bounce_dir'),
                $audio->preset->get('remix.bounce_dir'),
            ];
            foreach ($dirs as $dir) {
                $path = $dir . '/' . $this->property->file . '.tpl';
                if (file_exists($path)) {
                    break;
                }
            }
        }
        if (! $path) {
            throw new RemixException('bounce "' . $this->property->file . '.tpl" not found');
        }

        return Utility\Capture::capture(function () use ($path) {
            require($path);
        });
    }
    // function template()
}
// trait RecordableWithTemplate

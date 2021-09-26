<?php

namespace Remix\Studio;

use Remix\Instruments\Preset;
use Remix\RemixException;
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
      * [protected description]
      * @var Hash
      */
     protected $props = null;

     /**
      * source file
      * @var string
      */
     protected $file = '';

    protected $bounce_dir = [];

    public function loadTemplate(Preset $preset)
    {
        $this->bounce_dir = [
            'app' => $preset->get('app.pathes.bounce_dir'),
            'remix' => $preset->get('remix.pathes.bounce_dir'),
        ];
    }

    /**
     * Find and load a template file
     * @param  string $path  path of file
     * @return string        loaded template source
     */
    protected function template(string $path = null): string
    {
        if (! $path || ! file_exists($path)) {
            foreach ($this->bounce_dir as $dir) {
                $path = $dir . '/' . $this->file . '.tpl';
                if (file_exists($path)) {
                    break;
                }
            }
        }
        if (! $path || ! file_exists($path)) {
            throw new RemixException('bounce "' . $this->file . '" not found');
        }

        return Capture::capture(function () use ($path) {
            require($path);
        });
    }
    // function template()

    protected function bounceDir(string $key): ?string
    {
        return $this->bounce_dir[$key] ?? null;
    }
}
// trait RecordableWithTemplate

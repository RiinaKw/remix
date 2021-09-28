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

    protected static $bounce_dir = [];

    public static function loadTemplate(Preset $preset)
    {
        static::$bounce_dir = [
            'app' => $preset->get('app.pathes.bounce_dir'),
            'remix' => $preset->get('remix.pathes.bounce_dir'),
        ];
    }

    public static function findTemplateNS(string $path, string $ns)
    {
        $template = static::$bounce_dir[$ns] . '/' . $path . '.tpl';
        return file_exists($template) ? $template : null;
    }

    /**
     * Find and load a template file
     * @param  string $path         path of file
     * @param  string $namespace    namespace of file
     * @return string               loaded template source
     */
    protected function template(string $path = null, string $namespace = null): string
    {
        if (! $path || ! file_exists($path)) {
            if ($namespace) {
                $path = static::findTemplateNS($namespace);
            } else {
                foreach (array_keys(static::$bounce_dir) as $ns) {
                    $path = static::findTemplateNS($path, $ns);
                    if ($path) {
                        break;
                    }
                }
            }
        }
        if (! $path) {
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

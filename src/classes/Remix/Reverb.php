<?php

namespace Remix;

/**
 * Remix Reverb : web finalizer
 *
 * @package  Remix\Web
 * @todo Write the details.
 */
class Reverb extends Gear
{
    private $studio = null;
    private $preset = null;

    public function __construct(Studio $studio, Preset $preset)
    {
        if ($studio->hasTemplate()) {
            $studio->pathes($preset);
        }
        $this->studio = $studio;
        $this->preset = $preset;
    }

    public function __toString()
    {
        $output = $this->studio->output();
        $is_console = $this->studio->isConsole();
        $this->studio = null;

        if ($is_console) {
            $console = new Studio\Bounce($this->preset->get('remix.pathes.console_path'));
            $console->pathes($this->preset);
            $preset_arr = $this->preset->get();
            $this->preset = null;

            Delay::logMemory();
            Delay::logTime();

            $console->setHtml(
                'preset',
                \Utility\Dump::html(
                    $preset_arr,
                    [
                        'id_prefix' => 'remix-dump-',
                    ]
                )
            );

            $console->delay = Delay::get();
            $console_html = $console->record();
            unset($console);

            if (preg_match('/<\/body>/', $output)) {
                $output = str_replace(
                    '</body>',
                    $console_html . '</body>',
                    $output
                );
            } else {
                $output .= $console_html;
            }
        }
        return $output;
    }
}

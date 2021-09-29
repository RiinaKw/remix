<?php

namespace Remix;

use Remix\Instruments\Preset;

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
        parent::__construct();

        if ($studio->hasTemplate()) {
            $studio->preset($preset);
        }
        $this->studio = $studio;
        $this->preset = $preset;
    }

    public function __toString()
    {
        $output = $this->studio->output(true);
        $is_console = $this->studio->isConsole();
        $this->studio = null;

        if ($is_console) {
            Studio\Bounce::loadTemplate($this->preset);
            $console = new Studio\Bounce($this->preset->get('remix.pathes.console_path'));
            $preset_arr = $this->preset->get();
            unset($this->preset);

            Delay::log('NOTICE', 'Bounce of console cannot be destruct now');
            Delay::log('NOTICE', 'Reverb cannot be destruct now');

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
/*
var_dump(
  \Utility\Dump::html(
    \Utility\Http\Session::hash()->get()
  )
);

            $console->setHtml(
                'session',
                \Utility\Dump::html(
                    \Utility\Http\Session::hash()->get(),
                    [
                        'id_prefix' => 'remix-session-',
                    ]
                )
            );
*/
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

    public static function exeption(\Throwable $exception, Preset $preset): ?self
    {
        if ($exception instanceof Exceptions\HttpException) {
            $code = $exception->getStatusCode();
/*
            $bounce_dir = $preset->get('app.pathes.bounce_dir');;
            $bounce_path =  $bounce_dir . '/errors/' . $code . '.tpl';
*/
            Studio\Bounce::loadTemplate($preset);
            $name = 'errors/' . $code;
            $bounce_path = Studio\Bounce::findTemplateNS($name, 'app');

            if (! $bounce_path) {
                $bounce_path = Studio\Bounce::findTemplateNS('httperror', 'remix');
            }

            $compressor = new Studio\Compressor(
                $bounce_path,
                [
                    'satus_code' => $code,
                    'title' => \Utility\Http\StatusCode::get($code),
                    'message' => $exception->getMessage(),
                    'exception' => $exception,
                ]
            );
            $compressor->statusCode($code);

            return new static($compressor, $preset);
        }

        $status_code = 500;
        $traces = [];
        foreach ($exception->getTrace() as $item) {
            if (! isset($item['file']) || ! isset($item['line'])) {
                break;
            }
            $traces[] = [
                'trace' => $item,
                'source' => Monitor::getSource($item['file'], $item['line'], 5),
            ];
        }

        $template_path = $preset->get('remix.pathes.exception_path');
        if (! $template_path) {
            http_response_code(500);
            echo '<h1>Remix fatal error : Cannot render exception</h1>' . "\n";
            echo '<h2>Exception thrown : ' . $exception->getMessage() . '</h2>' . "\n";
            echo $exception->getFile() . ' in ' . $exception->getLine();
            //Monitor::dump($exception->getTrace());
            //Monitor::dump(Audio::getInstance()->preset);
            return null;
        }

        $compressor = new Studio\Compressor($template_path, [
            'status' => $status_code,
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'target' => Monitor::getSource($exception->getFile(), $exception->getLine(), 10),
            'traces' => $traces,
        ]);
        return new static($compressor, $preset);
    }
}

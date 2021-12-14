<?php

namespace Remix;

// Remix core
use Remix\Instruments\Preset;
use Remix\Studio\Bounce;
use Remix\Studio\Compressor;
// Utilities
use Utility\Http\Session;
use Utility\Http\StatusCode;
use Utility\Capture;
use Utility\Dump;
// Exceptions
use Throwable;
use Remix\Exceptions\CoreException;
use Remix\Exceptions\HttpException;

/**
 * Remix Reverb : web finalizer.
 *
 * @package  Remix\Web
 * @todo Write the details.
 */
class Reverb extends Gear
{
    /**
     * Response object to output
     * @var Studio
     */
    private $studio = null;

    /**
     * Preset object used to determine the template
     * @var Preset
     */
    private $preset = null;

    private $mixer = null;

    /**
     * Create finalizer.
     * @param Studio $studio  Response object to output
     * @param Preset $preset  Preset object used to determine the template
     */
    public function __construct(Studio $studio, Preset $preset)
    {
        parent::__construct();

        if ($this->audio) {
            $this->mixer = $this->audio->mixer;
            $this->audio->destroy();
            $this->audio = null;
        }

        if ($studio->hasTemplate()) {
            $studio->preset($preset);
        }
        $this->studio = $studio;
        $this->preset = $preset;

        $id = \Remix\Gear::getId($this);
        echo "Reverb[{$id}] is up.<br />\n";
    }
    // function __construct()

    public function __destruct()
    {
        parent::__destruct();

        $id = \Remix\Gear::getId($this);
        echo "Reverb[{$id}] is down.<br />\n";
    }

    /**
     * Render output.
     * @return string  Output string
     *
     * @todo Is this method too long?
     */
    public function __toString(): string
    {
        try {
            $output = null;
            Capture::capture(function () use (&$output) {
                $output = $this->studio->recorded();
            });

            $this->studio->sendHeader();
            $id = spl_object_id($this->studio);
            $is_console = $this->studio->isConsole();

            //$this->studio = null;

        } catch (Throwable $e) {
            // If an error occurs while rendering the Studio,
            // Reverb will render the exception directly
            return (string)static::exeption($e, $this->preset, true);
        }

        if ($is_console) {
            Bounce::loadTemplate($this->preset);
            $console = new Bounce($this->preset->get('remix.pathes.console_path'));
            $preset_arr = $this->preset->get();

            $this->audio = null;
            $this->preset = null;
            $this->studio = null;

            if ($this->mixer) {
                $this->mixer->destroy();
            }
            $this->mixer = null;

            echo "Rendering of Studio[{$id}] is finished. Now I'm not afraid of anything anymore, except for reverb and myself ...<br />\n";
            \Remix\Gear::dumpHash();
            echo "<br />\n";

            Delay::log('NOTICE', 'Bounce of console cannot be destruct now');
            Delay::log('NOTICE', 'Reverb cannot be destruct now');

            Delay::logMemory();
            Delay::logTime();

            // Dump preset
            $console->setHtml(
                'preset',
                Dump::html(
                    $preset_arr,
                    [
                        'id_prefix' => 'remix-dump-',
                    ]
                )
            );

            // Dump ssession
            $console->setHtml(
                'session',
                Dump::html(
                    Session::hash()->get(),
                    [
                        'id_prefix' => 'remix-session-',
                    ]
                )
            );

            // Dump delay
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
    // function __toString()

    /**
     * Render the exception.
     * @param  Throwable $exception  Exception to render
     * @param  Preset    $preset     Preset object used to determine the template
     * @return self|null
     *         New Reverb object,
     *         or null if the template not found and it rendered directly
     *
     * @todo Is this method too long?
     */
    public static function exeption(Throwable $exception, Preset $preset, bool $from_exception = false): ?self
    {
        if ($from_exception) {
            http_response_code(StatusCode::INTERNAL_SERVER_ERROR);
            echo '<h1>Remix fatal error : Exception loop</h1>' . "\n";
            echo '<h2>Exception thrown : ' . $exception->getMessage() . '</h2>' . "\n";
            echo $exception->getFile() . ' in ' . $exception->getLine();
            return null;
        }

        if ($exception instanceof HttpException) {
            $code = $exception->getStatusCode();

            Bounce::loadTemplate($preset);
            $name = 'errors/' . $code;
            $bounce_path = Bounce::findTemplateNS($name, 'app');

            if (! $bounce_path) {
                $bounce_path = Bounce::findTemplateNS('httperror', 'remix');
            }

            $compressor = new Compressor(
                $bounce_path,
                [
                    'satus_code' => $code,
                    'title' => StatusCode::get($code),
                    'message' => $exception->getMessage(),
                    'exception' => $exception,
                ]
            );
            $compressor->statusCode($code);

            return new static($compressor, $preset);
        }

        // Title mapping for each exception class
        $map = [
            'RemixException' => 'RemixException must not exist !!',
            'HttpException'  => 'HttpException should have already been caught ...?',
            'CoreException'  => 'Remix Core Error, Don\'t look at me !!',
            'AppException'   => 'App Config Error',
            'DJException'    => 'DJ Connection Error',
            'ErrorException' => 'PHP Error',
            'Error'          => 'PHP Syntax Error',
        ];

        // Determine the title to use from the class name
        $classname = basename(get_class($exception));
        $title = $map[$classname] ?? "Who is {$classname} ...?";

        // Get debug trace
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

        // Load the bounce needed to display the exception
        $template_path = $preset->get('remix.pathes.exception_path');

        if (! $template_path) {
            // Render directly if bounce could not be loaded
            http_response_code(StatusCode::INTERNAL_SERVER_ERROR);
            echo '<h1>Remix fatal error : Cannot render exception</h1>' . "\n";
            echo '<h2>Exception thrown : ' . $exception->getMessage() . '</h2>' . "\n";
            echo $exception->getFile() . ' in ' . $exception->getLine();
            return null;
        }

        // Create the exception renderer
        $compressor = new Compressor($template_path, [
            'status' => StatusCode::INTERNAL_SERVER_ERROR,
            'title' => $title,
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'target' => Monitor::getSource($exception->getFile(), $exception->getLine(), 10),
            'traces' => $traces,
        ]);
        $reverb = new static($compressor, $preset, true);
        return $reverb;
    }
    // function exeption()
}

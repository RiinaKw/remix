<?php

namespace Remix\Studio;

use Remix\Studio;
use Remix\Audio;
use Remix\Instruments\Preset;
use Utility\Http\StatusCode;

/**
 * Remix Maximizer : view renderer.
 *
 * @package  Remix\Web
 */
class Compressor extends Studio
{
    use Recordable;
    use RecordableWithTemplate;

    protected $bounce = null;

    /**
     * Build a bounce.
     * @param string $file    Path of emplate file
     * @param array  $params  Option parameters
     */
    public function __construct(string $file, array $params = [])
    {
        parent::__construct('html', $params);
        $this->statusCode($params['status'] ?? StatusCode::OK);
        $this->bounce = new Bounce($file, $params);
    }
    // function __construct()

    /**
     * Set Preset
     * @param Preset $preset
     *
     * @todo This is only used for bounce, but can it be done somewhere outside?
     */
    public function preset(Preset $preset)
    {
        Bounce::loadTemplate($preset);
    }
    // function preset()

    /**
     * Setter: Alias ​​for setEscaped()
     * @see static::setEscaped()
     */
    public function __set(string $name, $value): void
    {
        $this->bounce->setEscaped($name, $value);
    }
    // function __set()

    /**
     * HTML sanitize and set variables.
     * It runs htmlspecialchars() internally.
     *
     * @param string $name  Variable name used in the template
     * @param mixed $value  Variable value
     *
     * @todo The name and action do not match ...?
     */
    public function setEscaped(string $name, $value): void
    {
        $this->bounce->setEscaped($name, $value);
    }
    // function setEscaped()

    /**
     * Set the sanitized variable as HTML.
     * It DOES NOT run htmlspecialchars() internally.
     *
     * @param string $name  Variable name used in the template
     * @param mixed $value  Variable value
     */
    public function setHtml(string $name, $value): void
    {
        $this->bounce->setHtml($name, $value);
    }
    // function setHtml()

    /**
     * Render output.
     * @return string  Output string
     */
    public function record(): string
    {
        return $this->bounce->record();
    }
    // function record()
}

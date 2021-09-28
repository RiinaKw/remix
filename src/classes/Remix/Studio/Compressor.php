<?php

namespace Remix\Studio;

use Remix\Studio;
use Remix\Audio;
use Remix\Instruments\Preset;

/**
 * Remix Maximizer : view renderer
 *
 * @package  Remix\Web
 * @todo Write the details.
 */
class Compressor extends Studio
{
    use Recordable;
    use RecordableWithTemplate;

    protected $bounce = null;

    public function __construct(string $file, array $params = [])
    {
        parent::__construct('html', $params);
        $this->bounce = new Bounce($file, $params);
    }
    // function __construct()

    public function preset(Preset $preset)
    {
        Bounce::loadTemplate($preset);
    }

    public function __set(string $name, $value): void
    {
        $this->bounce->setEscaped($name, $value);
    }

    public function setEscaped(string $name, $value): void
    {
        $this->bounce->setEscaped($name, $value);
    }

    public function setHtml(string $name, $value): void
    {
        $this->bounce->setHtml($name, $value);
    }

    public function record(): string
    {
        return $this->bounce->record();
    }
    // function record()
}

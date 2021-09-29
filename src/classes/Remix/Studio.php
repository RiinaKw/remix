<?php

namespace Remix;

use Remix\Instruments\Preset;
use Utility\Hash;
use Utility\Arr;
use Utility\Http\MimeType;
use Utility\Http\StatusCode;
use Remix\Exceptions\HttpException;

/**
 * Remix Studio : web response manager
 *
 * @package  Remix\Web
 * @todo Write the details.
 */
class Studio extends Gear
{
    protected $property;

    public function __construct(string $type = 'none', $params = [])
    {
        parent::__construct();

        $this->property = new Hash();
        $this->property->type = $type;
        $this->property->status_code = 200;
        $this->property->is_console = false;

        // Make sure to set the mime type
        $this->contentType();

        if ($params instanceof Vinyl) {
            $this->property->params = $params->toArray();
        } else {
            $this->property->params = $params;
        }
    }
    // function __construct()

    public function __destruct()
    {
        $this->property->truncate();
        $this->property = null;
        parent::__destruct();
    }
    // function destroy()

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function preset(Preset $preset)
    {
    }

    /**
     * Does it implementing the trait Recordable?
     * @return bool  Implemented or not
     */
    public function recordable(): bool
    {
        return isset(class_uses($this)['Remix\\Studio\\Recordable']);
    }

    /**
     * Does it implementing the trait RecordableWithTemplate?
     * @return bool  Implemented or not
     */
    public function hasTemplate(): bool
    {
        return isset(class_uses($this)['Remix\\Studio\\RecordableWithTemplate']);
    }

    protected function contentType(string $forceType = '', string $charset = 'utf-8'): self
    {
        if ($forceType) {
            $mime_type = MimeType::get($forceType);
        } else {
            $mime_type = MimeType::get($this->property->type);
        }
        $this->property->is_console = $mime_type['console'];
        $this->property->push('headers', "Content-type: {$mime_type['type']}; charset={$charset}", 'Content-Type');
        $this->property->mime_type = $mime_type['type'];
        return $this;
    }

    public function statusCode(int $status_code = 200): self
    {
        $message = StatusCode::get($status_code);
        $status = "{$status_code} {$message}";
        $this->property->push('headers', "HTTP/1.1 {$status}");
        $this->property->status_code = $status_code;
        $this->property->status = $status;
        return $this;
    }
    // function status()

    public function getStatusCode(): int
    {
        return $this->property->status_code;
    }
    // function getStatusCode()

    public function getMimeType(): string
    {
        return $this->property->mime_type;
    }
    // function getMimeType()

    protected function sendHeader(): self
    {
        foreach ($this->property->headers as $header) {
            header($header);
        }
        return $this;
    }
    // function sendHeader()

    public function recorded(): string
    {
        $map = [
            'none' => function () {
                return '';
            },
            'text' => function () {
                $this->contentType();
                $params = $this->property->params;
                if (is_scalar($params)) {
                    return $params;
                } else {
                    return serialize($this->property->params);
                }
            },
            'closure' => function () {
                $this->contentType('text');
                $closure = $this->property->params;
                return $closure();
            },
            'json' => function () {
                $this->contentType();
                return json_encode($this->property->params);
            },
            'xml' => function () {
                $this->contentType();
                return Arr::toXML($this->property->params);
            },
            'redirect' => function () {
                header('Location: ' . $this->property->params);
                return '';
            },
        ];

        if (isset($map[$this->property->type])) {
            return $map[$this->property->type]();
        } else {
            if (method_exists($this, 'record')) {
                return $this->contentType('html')->record();
            } else {
                $this->contentType('text');
                return 'not recordable';
            }
        }
    }
    // function recorded()

    public function redirect(string $name, array $params = [], int $status_code = 303): self
    {
        $uri = Audio::getInstance()->mixer->uri($name, $params);

        $this->property->type = 'redirect';
        $this->property->params = $uri;
        $this->property->status_code = $status_code;
        return $this;
    }
    // function redirect()

    public function output(bool $sendHeader = true): string
    {
        $output = $this->recorded();
        if ($sendHeader) {
            $this->sendHeader();
        }
        return $output;
    }
    // function output()

    public function isConsole(): bool
    {
        return $this->property->type === 'html' && $this->property->is_console;
    }
}
// class Studio

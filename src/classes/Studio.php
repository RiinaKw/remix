<?php

namespace Remix;

use Remix\Preset\Http\MimeType;
use Remix\Preset\Http\StatusCode;
use Remix\Exceptions\HttpException;

/**
 * Remix Studio : web response manager
 */
class Studio extends Gear
{
    protected $property;

    public function __construct(string $type = 'none', $params = [])
    {
        parent::__construct();
        $this->property = new Utility\Hash();

        $this->property->type = $type;
        $this->property->status_code = 200;

        // Make sure to set the mime type
        $this->contentType();

        if ($params instanceof Vinyl) {
            $this->property->params = $params->toArray();
        } else {
            $this->property->params = $params;
        }
    }
    // function __construct()

    public function destroy(): void
    {
        $this->property = null;
    }
    // function destroy()

    protected function contentType(string $forceType = '', string $charset = 'utf-8'): self
    {
        if ($forceType) {
            $mime_type = MimeType::get($forceType);
        } else {
            $mime_type = MimeType::get($this->property->type);
        }
        Audio::getInstance()->console = $mime_type['console'];
        $this->property->push('headers', "Content-type: {$mime_type['type']}; charset={$charset}");
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
                return \Remix\Utility\Arr::toXML($this->property->params);
            },
            'redirect' => function () {
                header('Location: ' . $this->property->params);
                return '';
            },
            'header' => function () {
                $this->contentType('html');
                $bounce = new Bounce('httperror', [
                    'satus_code' => $this->property->status_code,
                    'title' => $this->property->params['title'],
                    'message' => $this->property->params['message'],
                ]);
                return $bounce->record();
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

    public function header(int $status_code, string $message = ''): self
    {
        $this->statusCode($status_code);
        $this->property->type = 'header';
        $this->property->status_code = $status_code;
        $this->property->params = [
            'title' => $this->property->status,
            'message' => $message
        ];
        Audio::getInstance()->console = true;
        return $this;
    }
    // function header()

    public function output(bool $sendHeader = true): string
    {
        $output = $this->recorded();
        if ($sendHeader) {
            $this->sendHeader();
        }
        return $output;
    }
    // function output()

    public function __toString(): string
    {
        return $this->output();
    }
    // function __toString()

    public static function recordException(\Throwable $exception): void
    {
        $status_code = 500;
        if ($exception instanceof HttpException) {
            $status_code = $exception->getStatusCode();
        }
        Audio::getInstance()->console = true;

        $view = new Bounce('exception', [
            'status' => $status_code,
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'target' => Monitor::getSource($exception->getFile(), $exception->getLine(), 10),
            'trace' => $exception->getTrace(),
        ]);
        echo $view->statusCode($status_code)->sendHeader()->record();
    }
    // function recordException()
}
// class Studio

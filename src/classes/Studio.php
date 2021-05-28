<?php

namespace Remix;

use Remix\Exceptions\HttpException;

/**
 * Remix Studio : web response manager
 */
class Studio extends Gear
{
    /*
    protected $params = [];
    protected $type = 'html';
    protected $code = 200;
    protected $status = '';
    protected $headers = [];
    */
    protected $property;

    public function __construct(string $type = 'none', $params = [])
    {
        parent::__construct();
        $this->property = new Utility\Hash();

        $this->property->type = $type;
        $this->property->code = 200;
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
            $mimetype = Preset\MimeType::get($forceType);
        } else {
            $mimetype = Preset\MimeType::get($this->property->type);
        }
        Audio::getInstance()->console = $mimetype['console'];
        $this->property->push('headers', "Content-type: {$mimetype['type']}; charset={$charset}");
        return $this;
    }

    public function status(int $code = 200): self
    {
        $message = Preset\StatusCode::get($code);
        $status = "{$code} {$message}";
        $this->property->push('headers', "HTTP/1.1 {$status}");
        $this->property->status = $status;
        return $this;
    }
    // function status()

    public function code(): int
    {
        return $this->property->code;
    }

    protected function sendHeader()
    {
        foreach ($this->property->headers as $header) {
            header($header);
        }
    }
    // function sendHeader()

    /**
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function recorded(): string
    {
        if ($this->property->code) {
            $this->status($this->property->code);
        }
        $output = '';

        switch ($this->property->type) {
            case 'none':
                $output =  '';
                break;

            case 'text':
                $this->contentType();
                $output = serialize($this->property->params);
                break;

            case 'closure':
                $this->contentType('text');
                $closure = $this->property->params;
                $output = $closure();
                break;

            case 'json':
                $this->contentType();
                $output = json_encode($this->property->params);
                break;

            case 'xml':
                $this->contentType();
                $output = \Remix\Utility\Arr::toXML($this->property->params);
                break;

            case 'redirect':
                header('Location: ' . $this->property->params);
                break;
                $output = '';

            case 'header':
                $this->contentType('html');
                $bounce = new Bounce('httperror', [], true);
                $bounce->satus_code = $this->property->code;
                $bounce->title = $this->property->params['title'];
                $bounce->message = $this->property->params['message'];
                $output = $bounce->record();
                break;

            default:
                if (method_exists($this, 'record')) {
                    $this->contentType('html');
                    $output = $this->record($this->property->params);
                } else {
                    $this->contentType('text');
                    $output = 'not recordable';
                }
                break;
        }
        // switch
        return $output;
    }
    // function recorded()

    public function redirect(string $name, array $params = [], int $code = 303): self
    {
        $uri = Audio::getInstance()->mixer->uri($name, $params);

        $this->property->type = 'redirect';
        $this->property->params = $uri;
        $this->property->code = $code;
        return $this;
    }
    // function redirect()

    public function header(int $code, string $message = ''): self
    {
        $this->status($code);
        $this->property->type = 'header';
        $this->property->code = $code;
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
        $code = 500;
        if ($exception instanceof HttpException) {
            $code = $exception->getStatus();
        }
        Audio::getInstance()->console = true;

        $view = new Bounce('exception', [
            'status' => $code,
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'target' => Monitor::getSource($exception->getFile(), $exception->getLine(), 10),
            'trace' => $exception->getTrace(),
        ], true);
        echo $view->status($code)->record();
    }
    // function recordException()
}
// class Studio

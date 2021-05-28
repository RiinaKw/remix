<?php

namespace Remix;

use Remix\Exceptions\HttpException;

/**
 * Remix Studio : web response manager
 */
class Studio extends Gear
{
    protected $params = [];
    protected $type = 'html';
    protected $code = 200;
    protected $status = '';
    protected $headers = [];

    public function __construct(string $type = 'none', $params = [])
    {
        parent::__construct();

        $this->type = $type;
        if ($params instanceof Vinyl) {
            $this->params = $params->toArray();
        } else {
            $this->params = $params;
        }
    }
    // function __construct()

    public function destroy(): void
    {
        $this->type = null;
        $this->params = null;
    }
    // function destroy()

    protected function contentType(string $forceType = '', string $charset = 'utf-8'): void
    {
        if ($forceType) {
            $mimetype = Preset\MimeType::get($forceType);
        } else {
            $mimetype = Preset\MimeType::get($this->type);
        }
        Audio::getInstance()->console = $mimetype['console'];
        $this->headers[] = "Content-type: {$mimetype['type']}; charset={$charset}";
    }

    public function status(int $code = 200): self
    {
        $message = Preset\StatusCode::get($code);
        $status = "{$code} {$message}";
        $this->headers[] = "HTTP/1.1 {$status}";
        $this->status = $status;
        return $this;
    }
    // function status()

    protected function sendHeader()
    {
        foreach ($this->headers as $header) {
            header($header);
        }
    }

    public function recorded(): string
    {
        $this->status($this->code);
        $output = '';

        switch ($this->type) {
            case 'none':
                $output =  '';
                break;

            case 'text':
                $this->contentType();
                $output = serialize($this->params);
                break;

            case 'closure':
                $this->contentType('text');
                $closure = $this->params;
                $output = $closure();
                break;

            case 'json':
                $this->contentType();
                $output = json_encode($this->params);
                break;

            case 'xml':
                $this->contentType();
                $output = \Remix\Utility\Arr::toXML($this->params);
                break;

            case 'redirect':
                header('Location: ' . $this->params);
                break;
                $output = '';

            case 'header':
                $this->contentType('html');
                $bounce = new Bounce('httperror', [], true);
                $bounce->satus_code = $this->code;
                $bounce->title = $this->params['title'];
                $bounce->message = $this->params['message'];
                $output = $bounce->record();
                break;

            default:
                if (method_exists($this, 'record')) {
                    $this->contentType('html');
                    $output = $this->record($this->params);
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

        $this->type = 'redirect';
        $this->params = $uri;
        $this->code = $code;
        return $this;
    }
    // function redirect()

    public function header(int $code, string $message = ''): self
    {
        $this->status($code);
        $this->type = 'header';
        $this->code = $code;
        $this->params = [
            'title' => $this->status,
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
            $status = $exception->getStatus();
        }
        Audio::getInstance()->console = true;

        $view = new Bounce('exception', [
            'status' => $status,
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

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
    protected $status = 200;
    protected $headers = [];

    protected static $mimetype = [
        'text' => [
            'type' => 'text/plain',
            'console' => false,
        ],
        'html' => [
            'type' => 'text/html',
            'console' => true,
        ],
        'json' => [
            'type' => 'application/json',
            'console' => false,
        ],
        'xml' => [
            'type' => 'application/xml',
            'console' => false,
        ],
        'stream' => [
            'type' => 'application/octet-stream',
            'console' => false,
        ],
    ];

    protected static $status_code = [
        '200' => 'OK',
        '201' => 'Created',
        '202' => 'Accepted',
        '203' => 'Non-Authoritative Information',
        '204' => 'No Content',
        '205' => 'Reset Content',
        '206' => 'Partial Content',

        '300' => 'Multiple Choice',
        '301' => 'Moved Permanently',
        '302' => 'Found',
        '303' => 'See Other',
        '304' => 'Not Modified',
        '307' => 'Temporary Redirect',
        '308' => 'Permanent Redirect',

        '400' => 'Bad Request',
        '401' => 'Unauthorized',
        '402' => 'Payment Required',
        '403' => 'Forbidden',
        '404' => 'Not Found',
        '405' => 'Method Not Allowed',
        '406' => 'Not Acceptable',
        '407' => 'Proxy Authentication Required',
        '408' => 'Request Timeout',
        '409' => 'Conflict',
        '410' => 'Gone',
        '411' => 'Length Required',
        '412' => 'Precondition Failed',
        '413' => 'Payload Too Large',
        '414' => 'URI Too Long',
        '415' => 'Unsupported Media Type',
        '416' => 'Range Not Satisfiable',
        '417' => 'Expectation Failed',
        '418' => 'I\'m a teapot',
        '421' => 'Misdirected Request',
        '425' => 'Too Early',
        '426' => 'Upgrade Required',
        '428' => 'Precondition Required',
        '429' => 'Too Many Requests',
        '431' => 'Request Header Fields Too Large',
        '451' => 'Unavailable For Legal Reasons',

        '500' => 'Internal Server Error',
        '501' => 'Not Implemented',
        '502' => 'Bad Gateway',
        '503' => 'Service Unavailable',
        '504' => 'Gateway Timeout',
        '505' => 'HTTP Version Not Supported',
        '506' => 'Variant Also Negotiates',
        '510' => 'Not Extended',
        '511' => 'Network Authentication Required',
    ];

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
            $mimetype = static::$mimetype[$forceType] ?? static::$mimetype['html'];
        } else {
            $mimetype = static::$mimetype[$this->type] ?? static::$mimetype['html'];
        }
        Audio::getInstance()->console = $mimetype['console'];
        $this->headers[] = "Content-type: {$mimetype['type']}; charset={$charset}";
    }

    public function status(int $code = 200): self
    {
        if (! isset(static::$status_code[$code])) {
            $code = '500';
        }

        $message = static::$status_code[$code];
        $this->headers[] = "HTTP/1.1 {$code} {$message}";

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
        $this->status($this->status);
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
                $bounce->code = $this->status;
                $bounce->message = $this->params;
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

    public function redirect(string $name, array $params = [], int $status = 303): self
    {
        $uri = Audio::getInstance()->mixer->uri($name, $params);

        $this->type = 'redirect';
        $this->params = $uri;
        $this->status = $status;
        return $this;
    }
    // function redirect()

    public function header(int $status, string $message = ''): self
    {
        $this->type = 'header';
        $this->status = $status;
        $this->params = $message;
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
        $status = 500;
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
        echo $view->status($status)->record();
    }
    // function recordException()
}
// class Studio

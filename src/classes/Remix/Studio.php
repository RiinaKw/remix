<?php

namespace Remix;

use Remix\Instruments\Preset;
use Utility\Hash;
use Utility\Arr;
use Utility\Http\MimeType;
use Utility\Http\StatusCode;
use Remix\Exceptions\HttpException;

/**
 * Remix Studio : web response manager.
 *
 * @package  Remix\Web
 */
class Studio extends Gear
{
    protected $property;

    /**
     * Create the response object.
     * @param string $type    Response type
     *                        Allow the following values :
     *                          * 'none' : no output
     *                          * 'text' : plain text
     *                          * 'html' : HTML text
     *                          * 'json' : JSON-encoded text
     *                          * 'xml' : XML-encoded text
     *                          * 'closure' : execute the function directly
     *                          * 'redirect' : Send only the redirect header without outputting
     * @param array  $params  Optional parameters
     */
    public function __construct(string $type = 'none', $params = [])
    {
        parent::__construct();

        $this->property = new Hash();
        $this->property->type = $type;
        $this->property->params = $params;

        // Does not output debug console by default
        $this->property->is_console = false;

        $this->statusCode(StatusCode::OK);

        // Make sure to set the mime type
        $this->contentType();
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
     * @todo This is only overridden by Compressor ... should it be here?
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

    /**
     * Set mime type.
     *
     * @param  string $forceType  Specify when forcibly changing the mime type
     * @param  string $charset    Output character code
     * @return self               Itself
     */
    protected function contentType(?string $forceType = null, string $charset = 'utf-8'): self
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

    /**
     * Set HTTP status code.
     * @param  integer $status_code  3-digit status code
     * @return self                  Itself
     */
    public function statusCode(int $status_code = StatusCode::OK): self
    {
        $message = StatusCode::get($status_code);
        $status = "{$status_code} {$message}";
        $this->property->push('headers', "HTTP/1.1 {$status}");
        $this->property->status_code = $status_code;
        $this->property->status = $status;
        return $this;
    }
    // function status()

    /**
     * Get the HTTP status code.
     * @return int  3-digit status code
     */
    public function getStatusCode(): int
    {
        return $this->property->status_code;
    }
    // function getStatusCode()

    /**
     * Get the mime type.
     * @return string  Mime type strings such as "text/html"
     */
    public function getMimeType(): string
    {
        return $this->property->mime_type;
    }
    // function getMimeType()

    /**
     * Send all headers.
     * @return self  Itself
     */
    public function sendHeader(): self
    {
        foreach ($this->property->headers as $header) {
            header($header);
        }
        return $this;
    }
    // function sendHeader()

    /**
     * Render the response.
     * Send headers as needed.
     *
     * @return string  Rendered output string
     */
    public function recorded(): string
    {
        // Closure mapping for each output type
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
                $this->property->push('headers', "Location: {$this->property->params}");
                $this->sendHeader();
                return '';
            },
        ];

        if (isset($map[$this->property->type])) {
            $result = $map[$this->property->type]();

            if (! is_string($result)) {
                // ill-behaved closure : it did not return a string
                throw new RemixException("{$this->property->type} must return string");
            }
            return $result;
        } else {
            if (method_exists($this, 'record')) {
                // Output as HTML
                return $this->contentType('html')->record();
            } else {
                $this->contentType('text');
                return 'not recordable';
            }
        }
    }
    // function recorded()

    /**
     * Do redirection.
     *
     * @param  string  $name         Route name defined in Mixer
     * @param  array   $params       Parameters required to build the route
     * @param  integer $status_code  HTTP status code used for redirect
     * @return self                  Itself
     *
     * @todo Should this exist in Lyric?
     */
    public function redirect(string $name, array $params = [], int $status_code = 303): self
    {
        $uri = Audio::getInstance()->mixer->uri($name, $params);

        $this->property->type = 'redirect';
        $this->property->params = $uri;
        $this->property->status_code = $status_code;
        return $this->statusCode($status_code);
    }
    // function redirect()

    /**
     * Get the URI of the redirect destination.
     * @return string|null  URI, null if not redirect
     */
    public function getRedirectUri(): ?string
    {
        if ($this->property->type !== 'redirect') {
            return null;
        }
        return $this->property->params;
    }
    // function getRedirectUri()

    /**
     * Need to output a debug console?
     * @return bool  Needed or not
     */
    public function isConsole(): bool
    {
        return $this->property->type === 'html' && $this->property->is_console;
    }
    // function isConsole()
}
// class Studio

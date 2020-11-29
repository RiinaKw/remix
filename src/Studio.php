<?php

namespace Remix;

/**
 * Remix Studio : web response manager
 */
class Studio extends \Remix\Component
{
    protected $params = [];
    protected $type = 'html';
    protected $status = 200;

    public function __construct(string $type = 'none', $params = [])
    {
        parent::__construct();

        $this->type = $type;
        if ($params instanceof Vinyl) {
            $this->params = $params->toArray();
        } else {
            $this->params = $params;
        }
    } // function __construct()

    public function destroy()
    {
        $this->type = null;
        $this->params = null;
    }

    public function __toString() : string
    {
        $this->status($this->status);
        switch ($this->type) {
            case 'none':
                return '';

            case 'text':
                return serialize($this->params);

            case 'closure':
                $closure = $this->params;
                return $closure();

            case 'json':
                return json_encode($this->params);

            case 'xml':
                return \Remix\Utility\Arr::toXML($this->params);

            case 'redirect':
                header('Location: ' . $this->params);
                return '';

            case 'header':
                http_response_code($this->status);
                $bounce = new Bounce('httperror');
                $bounce->code = $this->status;
                $bounce->message = $this->params;
                return $bounce->record();

            default:
                if (method_exists($this, 'record')) {
                    return $this->record($this->params);
                } else {
                    return 'not recordable';
                }
        } // switch
    } // function __toString()

    public function status(int $code = 200) : self
    {
        http_response_code($code);
        return $this;
    } // function status()

    public function json($params) : self
    {
        $this->type = 'json';
        if ($params instanceof Vinyl) {
            $this->params = $params->toArray();
        } else {
            $this->params = $params;
        }
        return $this;
    } // function json()

    public function xml($params) : self
    {
        $this->type = 'xml';
        if ($params instanceof Vinyl) {
            $this->params = $params->toArray();
        } else {
            $this->params = $params;
        }
        return $this;
    } // function json()

    public function redirect(string $name, array $params = [], int $status = 303) : self
    {
        $mixer = App::getInstance()->mixer();
        $uri = $mixer->uri($name, $params);

        $this->type = 'redirect';
        $this->params = $uri;
        $this->status = $status;
        return $this;
    } // function redirect()

    public function header(int $status, string $message = '') : self
    {
        $this->type = 'header';
        $this->status = $status;
        $this->params = $message;
        return $this;
    }

    public static function recordException($e) : void
    {
        $status = 500;
        if ($e instanceof Exceptions\HttpException) {
            $status = $e->getStatus();
        }

        $view = new Bounce('exception', [
            'status' => $status,
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'target' => Monitor::getSource($e->getFile(), $e->getLine(), 10),
        ]);
        echo $view->status($status)->record();
    } // function recordException()
} // class Studio

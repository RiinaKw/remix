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
        \Remix\App::getInstance()->logBirth(__METHOD__);

        $this->type = $type;
        $this->params = $params;
    } // function __construct()

    public function __destruct()
    {
        \Remix\App::getInstance()->logDeath(__METHOD__);
        parent::__destruct();
    } // function __destruct()

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
                return $this->params;

            case 'closure':
                $closure = $this->params;
                return $closure();

            case 'json':
                return json_encode($this->params);

            case 'redirect':
                header('Location: ' . $this->params);
                return '';

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
        $this->params = $params;
        return $this;
    } // function json()

    public function redirect(string $name, int $status = 303) : self
    {
        $mixer = App::getInstance()->mixer();
        $track = $mixer->named($name);
        $uri = $track->uri();

        $this->type = 'redirect';
        $this->params = $uri;
        $this->status = $status;
        return $this;
    } // function redirect()

    public static function recordException($e) : void
    {
        //Debug::dump($e);

        $status = 500;
        if ($e instanceof Exceptions\HttpException) {
            $status = $e->getStatus();
        }

        $view = new Bounce('exception', [
            'status' => $status,
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'target' => Debug::getSource($e->getFile(), $e->getLine(), 10),
        ]);
        echo $view->status($status)->record();
    } // function recordException()
} // class Studio

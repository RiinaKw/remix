<?php

namespace Remix;

/**
 * Remix Studio : web response manager
 */
class Studio extends \Remix\Component
{
    protected $params = [];
    protected $type = 'html';

    public function __construct(string $type = 'none', $params = [])
    {
        $this->type = $type;
        $this->params = $params;
    } // function __construct()

    public function __toString() : string
    {
        switch ($this->type) {
            case 'none':
                return '';

            case 'closure':
                $closure = $this->params;
                return $closure();

            case 'json':
                return json_encode($this->params);

            default:
                if (method_exists($this, 'record')) {
                    return $this->record($this->params);
                } else {
                    return 'not recordable';
                }
        } // switch
    } // function __toString()

    public function status(int $code = 200)
    {
        http_response_code($code);
        return $this;
    } // function status()

    public function json($params)
    {
        $this->type = 'json';
        $this->params = $params;
        return $this;
    } // function json()

    public function redirect(string $name, int $status = 303)
    {
        $mixer = App::getInstance()->mixer();
        $track = $mixer->named($name);
        $uri = $track->uri();
        $this->status(status);
        header('Location: ' . $uri);
    } // function redirect()

    public static function recordException($e)
    {
        //Debug::dump($e);
        $target = Debug::getSource($e->getFile(), $e->getLine(), 10);

        $status = 500;
        if ($e instanceof Exceptions\HttpException) {
            $status = $e->getStatus();
        }

        $view = new Bounce('exception', [
            'status' => $status,
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'target' => '<ol class="source">' . implode("\n", $target) . '</ol>',
        ]);
        echo $view->status($status)->record();
    } // function recordException()
} // class Studio

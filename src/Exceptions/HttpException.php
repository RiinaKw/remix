<?php

namespace Remix\Exceptions;

class HttpException extends \Remix\RemixException
{
    protected $status = 200;

    public function __construct(string $message, int $status = 200)
    {
        parent::__construct($message);
        $this->status = $status;
    } // function __construct()

    public function getStatus()
    {
        return $this->status;
    }
/*
    public function render()
    {
        $target = \Remix\Debug::getSource($this->file, $this->line, 10);

        $view = new \Remix\Bounce('exception', [
            'status' => $this->status,
            'message' => $this->message,
            'file' => $this->file,
            'line' => $this->line,
            'target' => implode("\n", $target),
        ]);
        echo $view->status($this->status)->render();
    } // function render()
*/
} // class HttpException

<?php

namespace Remix\Exceptions;

class HttpException extends \RuntimeException
{
    protected $status;

    public function __construct(string $message, int $status)
    {
        parent::__construct($message);
        $this->status = $status;
    } // function __construct()

    public function render()
    {
        http_response_code($this->status);

        $target = \Remix\Debug::getSource($this->file, $this->line, 10);

        $view = new \Remix\Bounce;
        $view->render('exception', [
            'status' => $this->status,
            'message' => $this->message,
            'file' => $this->file,
            'line' => $this->line,
            'target' => implode("\n", $target),
        ]);

        \Remix\Debug::dump($this);
    } // function render()
} // class HttpException

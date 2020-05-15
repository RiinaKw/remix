<?php

namespace Remix;

/**
 * Remix Bounce : view renderer
 */
class Bounce extends \Remix\Component
{

    protected $source;

    public function __construct()
    {
        parent::__construct();
        $this->source = 'from bounce : {{ $var }}';
    } // function __construct()

    public function render(string $file, array $params = [])
    {
        $remix = \Remix\App::getInstance();
        $bounce_dir = $remix->config()->get('app.bounce_dir');
        $path = $remix->dir($bounce_dir . '/' . $file . '.php');

        ob_start();
        require($path);
        $source = ob_get_clean();

        foreach ($params as $key => $value) {
            $target = '{{ $' . $key . ' }}';
            $source = str_replace($target, $value, $source);
        }
        echo $source;
    } // function render()
} // class Bounce

<?php

namespace Remix;

/**
 * Remix Bounce : view renderer
 */
class Bounce extends \Remix\Studio
{
    use \Remix\Renderable;

    protected $source;
    protected $file;

    public function __construct(string $file, array $params)
    {
        parent::__construct('html', $params);
        $this->source = 'from bounce : {{ $var }}';
        $this->file = $file;
    } // function __construct()

    public function render() : string
    {
        $remix = App::getInstance();
        $bounce_dir = $remix->config()->get('app.bounce_dir');
        $path = $remix->dir($bounce_dir . '/' . $this->file . '.tpl');

        ob_start();
        require($path);
        $source = ob_get_clean();

        foreach ($this->params as $key => $value) {
            $target = '{{ $' . $key . ' }}';
            $source = str_replace($target, $value, $source);
        }
        return $source;
    } // function render()
} // class Bounce

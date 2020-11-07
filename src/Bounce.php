<?php

namespace Remix;

/**
 * Remix Bounce : view renderer
 */
class Bounce extends \Remix\Studio
{
    use \Remix\Recordable;

    protected $source;
    protected $file;

    public function __construct(string $file, array $params)
    {
        parent::__construct('html', $params);
        $this->source = 'from bounce : {{ $var }}';
        $this->file = $file;
    } // function __construct()

    public function record() : string
    {
        $remix = App::getInstance();
        $bounce_dir = $remix->config()->get('app.bounce_dir');
        $path = $remix->dir($bounce_dir . '/' . $this->file . '.tpl');

        ob_start();
        require($path);
        $source = ob_get_clean();

        return $this->run($source);
    } // function record()

    protected function run($source)
    {
        $executable = $source;
        $executable = preg_replace(
            '/\{\{\s*(for|while|foreach|if|elseif)\s+?([^\s].*?[^\s])\s*\}\}/',
            '{{ $1 ($2) }}',
            $executable
        );
        $executable = preg_replace(
            '/\{\{\s*(for|while|foreach|if|elseif)\s+\(\((.*?)\)\)\s*\}\}/',
            '<?php $1 ($2) : ?>',
            $executable
        );
        $executable = preg_replace(
            '/\{\{\s*(for|while|foreach|if|elseif)\s+(.*?)\s*\}\}/',
            '<?php $1 $2 : ?>',
            $executable
        );
        $executable = preg_replace(
            '/\{\{\s*(else)\s*\}\}/',
            '<?php $1 : ?>',
            $executable
        );
        $executable = preg_replace(
            '/\{\{\s*(endfor|endwhile|endforeach|endif)\s*\}\}/',
            '<?php $1; ?>',
            $executable
        );
        $executable = '?>' . preg_replace('/\{\{\s*(.*?)\s*\}\}/', '<?php echo $1; ?>', $executable) . '<?php';
        extract($this->params);

        ob_start();
        eval($executable);
        $response = ob_get_clean();

        return $response;
    }
} // class Bounce

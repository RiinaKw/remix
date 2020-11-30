<?php

namespace Remix;

/**
 * Remix Bounce : view renderer
 */
class Bounce extends \Remix\Studio
{
    use \Remix\Recordable;

    protected static $left_delimiter = '{{';
    protected static $right_delimiter = '}}';

    protected $file;
    protected $escaped_params = [];
    protected $html_params = [];

    public function __construct(string $file, array $params = [])
    {
        parent::__construct('html', $params);
        $this->file = $file;
        $this->escaped_params = $params;
    }
    // function __construct()

    public function __set(string $name, $value): void
    {
        $this->escaped_params[$name] = $value;
    }

    public function setHtml(string $name, $value): void
    {
        $this->html_params[$name] = $value;
    }

    public function record(): string
    {
        $remix = App::getInstance();
        $bounce_dir = $remix->preset()->get('app.bounce_dir');
        $path = $remix->dir($bounce_dir . '/' . $this->file . '.tpl');

        if (! $path) {
            throw new \Remix\RemixException('bounce "' . $this->file . '.tpl" not found');
        }

        $source = \Remix\Utility\Capture::capture(function () use ($path) {
            require($path);
        });

        return $this->run($source);
    }
    // function record()

    protected function translate(string $source): string
    {
        $re_l = '/' . static::$left_delimiter . '\s*';
        $re_r = '\s*' . static::$right_delimiter . '/';
        $executable = $source;

        // translate to php
        foreach (array_keys($this->html_params) as $key) {
            $executable = preg_replace(
                $re_l . '(\$' . $key . ')' . $re_r,
                '<?php echo $1 ?? null; ?>',
                $executable
            );
        }
        $executable = preg_replace(
            $re_l . '(for|while|foreach|if|elseif)\s+?([^\s].*?[^\s])' . $re_r,
            '{{ $1 ($2) }}',
            $executable
        );
        $executable = preg_replace(
            $re_l . '(for|while|foreach|if|elseif)\s+\(\((.*?)\)\)' . $re_r,
            '<?php $1 ($2) : ?>',
            $executable
        );
        $executable = preg_replace(
            $re_l . '(for|while|foreach|if|elseif)\s+(.*?)' . $re_r,
            '<?php $1 $2 : ?>',
            $executable
        );
        $executable = preg_replace(
            $re_l . '(else)' . $re_r,
            '<?php $1 : ?>',
            $executable
        );
        $executable = preg_replace(
            $re_l . '(endfor|endwhile|endforeach|endif)' . $re_r,
            '<?php $1; ?>',
            $executable
        );

        $executable = '?>'
            . preg_replace(
                $re_l . '(.*?)' . $re_r,
                '<?php echo \Remix\Utility\Str::h($1 ?? null); ?>',
                $executable
            ) . '<?php';

        return $executable;
    }

    /**
     * @SuppressWarnings(PHPMD.EvalExpression)
     */
    protected function run($source): string
    {
        $escaped_params = $this->escaped_params;
        $html_params = $this->html_params;

        $response = \Remix\Utility\Capture::capture(function () use ($source, $escaped_params, $html_params) {
            $executable = $this->translate($source);

            extract($escaped_params);
            extract($html_params);

            eval($executable);
        });

        return $response;
    }
}
// class Bounce

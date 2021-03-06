<?php

namespace Remix;

/**
 * Remix Bounce : view renderer
 */
class Bounce extends Studio
{
    use Recordable;
    use RecordableWithTemplate;

    protected static $left_delimiter = '{{';
    protected static $right_delimiter = '}}';

    public function __construct(string $file, array $params = [])
    {
        parent::__construct('html', $params);
        $this->property->file = $file;
        $this->property->escaped_params = $params;
    }
    // function __construct()


    public function record(string $path = null): string
    {
        $source = static::template($path);
        return $this->play($source);
    }
    // function record()

    protected function translate(string $source): string
    {
        $re_l = '/' . static::$left_delimiter . '\s*';
        $re_r = '\s*' . static::$right_delimiter . '/';
        $executable = $source;

        // translate to php
        if ($this->property->html_params) {
            foreach (array_keys($this->property->html_params) as $key) {
                $executable = preg_replace(
                    $re_l . '(\$' . $key . ')' . $re_r,
                    '<?php echo $1 ?? null; ?>',
                    $executable
                );
            }
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
    // function translate()

    /**
     * @SuppressWarnings(PHPMD.EvalExpression)
     */
    protected function play($source): string
    {
        $escaped_params = $this->property->escaped_params;
        $html_params = $this->property->html_params;

        $response = \Remix\Utility\Capture::capture(
            function () use ($source, $escaped_params, $html_params) {
                extract($escaped_params);
                if ($html_params) {
                    extract($html_params);
                }
                $executable = $this->translate($source);
                eval($executable);
            }
        );

        return $response;
    }
    // function play()
}
// class Bounce

<?php

namespace Remix\Studio;

use Remix\Studio;
use Remix\Delay;
use Utility\Capture;

/**
 * Remix Bounce : view renderer
 *
 * @package  Remix\Web
 * @todo Write the details.
 */
class Bounce extends Studio
{
    use Recordable;
    use RecordableWithTemplate;

    protected static $preset = null;

    protected static $left_delimiter = '{{';
    protected static $right_delimiter = '}}';

    protected static $bounce_dir = [];

    public function __construct(string $file, array $params = [])
    {
        $this->log_param = $file;
        parent::__construct('html', $params);

        $this->property->file = $file;
        $this->property->escaped_params = $params;
    }
    // function __construct()

    public function record(): string
    {
        $this->property->source = $this->template($this->property->file);
        return $this->play($this->property->source);
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

        $bounce_dir = static::$bounce_dir['app'];
        $executable = preg_replace(
            $re_l . 'include\s+(.+?)' . $re_r,
            "<?php include('{$bounce_dir}/$1'); ?>",
            $executable
        );

        $executable = preg_replace(
            $re_l . 'exec\s*(\$.+?)' . $re_r,
            '<?php if ( $1 instanceof Remix\\Studio && $1->recordable() ) { echo $1->record(); } ?>',
            $executable
        );

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
                '<?php echo \Utility\Str::h($1 ?? null); ?>',
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

        $response = Capture::capture(
            function () use ($source, $escaped_params, $html_params) {
                extract($escaped_params);
                if ($html_params) {
                    extract($html_params);
                }
                $executable = $this->translate($source);

                //echo nl2br(htmlspecialchars($executable));
                //exit;

                eval($executable);
            }
        );

        return $response;
    }
    // function play()
}
// class Bounce
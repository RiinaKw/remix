<?php

namespace Remix\Studio;

use Remix\Gear;
use Remix\Studio;
use Remix\Delay;
use Utility\Hash;
use Utility\Capture;

/**
 * Remix Bounce : view renderer
 *
 * @package  Remix\Web
 * @todo Write the details.
 */
class Bounce extends Gear
{
    use Recordable;
    use RecordableWithTemplate;

    /**
     * source php
     * @var string
     */
    protected $source = '';

    protected static $preset = null;

    protected static $left_delimiter = '{{';
    protected static $right_delimiter = '}}';

    public function __construct(string $file, array $params = [])
    {
        $this->log_param = $file;
        parent::__construct();

        $this->file = $file;
        $this->props = new Hash();
        $this->props->escaped_params = $params;
    }
    // function __construct()

/*
    public function __set(string $name, $value): void
    {
        $this->setEscaped($name, $value);
    }

    public function setEscaped(string $name, $value): void
    {
        $this->props->push('escaped_params', $value, $name);
    }

    public function setHtml(string $name, $value): void
    {
        $this->props->push('html_params', $value, $name);
    }
*/

    public function record(): string
    {
        $this->source = $this->template($this->file);
        return $this->play($this->source);
    }
    // function record()

    protected function translate(string $source): string
    {
        $re_l = '/' . static::$left_delimiter . '\s*';
        $re_r = '\s*' . static::$right_delimiter . '/';
        $executable = $source;

        // translate to php
        if ($this->props->html_params) {
            foreach (array_keys($this->props->html_params) as $key) {
                $executable = preg_replace(
                    $re_l . '(\$' . $key . ')' . $re_r,
                    '<?php echo $1 ?? null; ?>',
                    $executable
                );
            }
        }

        $bounce_dir = $this->bounceDir('app');
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
    protected function play(string $source): string
    {
        $escaped_params = $this->props->escaped_params;
        $html_params = $this->props->html_params;

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

<?php

namespace RemixDemo\Synthesizers;

use Remix\Synthesizer;
use Remix\Filter;
use Remix\Oscillators\{Email};

/**
 * Example synthesizers for example form.
 *
 * @package  Demo\Synthesizers
 */
class FormSynthesizer extends Synthesizer
{
    /**
     * filters definition
     *
     * @return array<int, \Remix\Filter>
     */
    protected function filters(): array
    {
        return [
            Filter::define('name', 'your name')->rules(['required', 'max:20']),
            Filter::define('email', 'your mail address')->rules(['required'])->rules(new Email()),
            Filter::define('profile', 'your profile etc'),
        ];
    }
}
// class TopChannel

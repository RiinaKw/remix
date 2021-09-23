<?php

namespace Remix\Demo\Synthesizer;

use Remix\Synthesizer;
use Remix\Filter;

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
            Filter::define('name', 'your name')->rules('required')->rules('max:5'),
            Filter::define('email', 'your mail address')->rules(['required'])->rules(new \Remix\Oscillators\Email()),
        ];
    }
}
// class TopChannel

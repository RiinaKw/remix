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
            Filter::factory('name', 'your name')->rules('required|max:5'),
            Filter::factory('email', 'your mail address')->rules('required|email'),
        ];
    }
}
// class TopChannel

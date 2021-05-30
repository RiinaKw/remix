<?php

namespace App\Channel;

use Remix\Sampler;
use Remix\Studio;
use Remix\Bounce;

class SampleChannel extends \Remix\Channel
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function index(Sampler $sampler): Studio
    {
        return new Bounce('sample/index', [
            'title' => 'Remix example',
        ]);
    }
    // function index()
}
// class SampleChannel

<?php

namespace RemixDemo\Channel;

use Remix\Sampler;
use Remix\Studio;
use Remix\Bounce;

class TopChannel extends \Remix\Channel
{
    public function index()
    {
        return 'I am your Remix.';
    }
    // function index()

    public function vader(Sampler $sampler): Studio
    {
        $bounce = new Bounce('vader');
        $bounce->name = $sampler->params('name', 'Luke');
        return $bounce;
    }
    // function bounce()
}
// class TopChannel

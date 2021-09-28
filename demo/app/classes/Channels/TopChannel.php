<?php

namespace Remix\Demo\Channels;

use Remix\Sampler;
use Remix\Studio;
use Remix\Studio\Compressor;

class TopChannel extends \Remix\Channel
{
    public function index()
    {
        return 'I am your Remix.';
    }
    // function index()

    public function vader(Sampler $sampler): Studio
    {
        throw new \Remix\Exceptions\HttpException('Test', 400);
        $bounce = new Compressor('vader');
        $bounce->name = $sampler->params('name', 'Luke');
        $bounce->type = $sampler->get('type', 'father');
        return $bounce;
    }
    // function bounce()
}
// class TopChannel

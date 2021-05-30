<?php

namespace App\Channel;

use Remix\Sampler;
use Remix\Studio;
use Remix\Bounce;
use Remix\Exceptions\HttpException;

class SampleChannel extends \Remix\Channel
{
    public function index(): Studio
    {
        return new Bounce('sample/index', [
            'title' => 'Remix example',
        ]);
    }
    // function index()

    public function exception(Sampler $sampler): Studio
    {
        $code = $sampler->param('code', 500);

        $message = 'exception test from ' . __METHOD__
            . ' with status code ' . $code;
        throw new HttpException($message, $code);
    }
    // function exception()
}
// class SampleChannel

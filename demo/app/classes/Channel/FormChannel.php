<?php

namespace RemixDemo\Channel;

use Remix\Sampler;
use Remix\Studio;
use Remix\Bounce;

class FormChannel extends \Remix\Channel
{
    public function index(): Studio
    {
        $bounce = new Bounce('form/index');
        return $bounce;
    }
    // function index()

    public function submit(Sampler $sampler): Studio
    {
        $bounce = new Bounce('form/submit');
        $bounce->name = $sampler->post('name', 'empty');
        $bounce->email = $sampler->post('email', 'empty');
        return $bounce;
    }
    // function submit()
}
// class FormChannel

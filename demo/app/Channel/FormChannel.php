<?php

namespace App\Channel;

use Remix\Audio;
use Remix\Sampler;
use Remix\Studio;
use Remix\Bounce;

class FormChannel extends \Remix\Channel
{
    public function index(Sampler $sampler): Studio
    {
        $mixer = Audio::getInstance()->mixer;

        $id = $sampler->param('id');

        $bounce = new Bounce('form/index');
        $bounce->url = $mixer->uri('form', [':id' => $id]);
        return $bounce;
    }
    // function index()

    public function post(Sampler $sampler): Studio
    {
        $id = $sampler->param('id');
        $title = $sampler->post('title');

        $bounce = new Bounce('form/result');
        $bounce->id = $id;
        $bounce->title = $title;
        return $bounce;
    }
    // function index()
}
// class ApiChannel

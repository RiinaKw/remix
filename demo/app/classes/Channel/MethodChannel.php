<?php

namespace App\Channel;

use Remix\Sampler;
use Remix\Studio;

class MethodChannel extends \Remix\Channel
{
    public function index()
    {
        return __METHOD__;
    }
    // function index()
}
// class MethodChannel

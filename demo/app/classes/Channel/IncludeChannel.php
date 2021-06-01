<?php

namespace App\Channel;

use Remix\Sampler;
use Remix\Studio;
use Remix\Monitor;
use Remix\Bounce;

class IncludeChannel extends \Remix\Channel
{
    public function index(Sampler $sampler): Studio
    {
        $in = new Bounce('include_test/internal');
        $in->internal_param = 'i am include_test/internal';
        //var_dump( $in->recordable() );
        //exit;

        $ex = new Bounce('include_test/external');
        $ex->external_param = 'i am include_test/external';

        $ex->in = $in;
        return $ex;
    }
    // function test()
}
// class IncludeChannel

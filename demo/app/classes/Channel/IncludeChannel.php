<?php

namespace App\Channel;

use Remix\Sampler;
use Remix\Studio;
use Remix\Monitor;
use Remix\Bounce;

class IncludeChannel extends \Remix\Channel
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function after(Sampler $sampler, Studio $studio): Studio
    {
        $layout = new Bounce('include_test/external');
        $layout->external_param = 'i am include_test/external';
        $layout->in = $studio;
        return $layout;
    }

    public function index(): Studio
    {
        $bounce = new Bounce('include_test/internal');
        $bounce->internal_param = 'i am include_test/internal';
        return $bounce;
    }
    // function test()
}
// class IncludeChannel

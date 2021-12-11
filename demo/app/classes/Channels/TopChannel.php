<?php

namespace RemixDemo\Channels;

use Remix\Sampler;
use Remix\Studio;
use Remix\Studio\Compressor;

/**
 * Channel Example for top.
 *
 * @package  Demo\Channels
 */
class TopChannel extends \Remix\Channel
{
    /**
     * Simply return a string.
     * @return string
     */
    public function index()
    {
        return 'I am your Remix.';
    }
    // function index()

    /**
     * Example of Sampler and input parameters.
     * @param  Sampler $sampler  Input sampler
     * @return Studio
     */
    public function vader(Sampler $sampler): Studio
    {
        //throw new \Remix\Exceptions\HttpException('Test', 400);
        $bounce = new Compressor('vader');
        $bounce->name = $sampler->params('name', 'Luke');
        $bounce->type = $sampler->get('type', 'father');
        return $bounce;
    }
    // function bounce()

    /**
     * Raise a redirect.
     * @return Studio
     */
    public function redirect302(): Studio
    {
        return (new Studio())->redirect('redirected', [], 302);
    }

    /**
     * Where the redirect will reach.
     * @return string
     */
    public function redirected(): string
    {
        return 'redirected';
    }
}
// class TopChannel

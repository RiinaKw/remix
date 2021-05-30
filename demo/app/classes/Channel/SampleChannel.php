<?php

namespace App\Channel;

use Remix\Sampler;
use Remix\Studio;
use Remix\Bounce;
use Remix\Preset\Http\StatusCode;
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

    public function xml(): Studio
    {
        $params = [
            'some' => 'thing',
        ];
        return Studio::factory('xml', $params);
    }
    // function xml()

    public function json(): Studio
    {
        $params = [
            'some' => 'thing',
        ];
        return Studio::factory('json', $params);
    }
    // function json()

    public function error(Sampler $sampler): Studio
    {
        $code = $sampler->param('code', 500);

        $bounce = new Bounce('sample/error', [
            'title' => 'Remix example with http status code',
            'code' => $code,
            'message' => StatusCode::get($code),
        ]);
        return $bounce->statusCode($code);
    }
    // function error()

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

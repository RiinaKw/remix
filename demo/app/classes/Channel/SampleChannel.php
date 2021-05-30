<?php

namespace App\Channel;

use Remix\Audio;
use Remix\Sampler;
use Remix\Studio;
use Remix\Bounce;
use Remix\Preset\Http\StatusCode;
use Remix\Exceptions\HttpException;

class SampleChannel extends \Remix\Channel
{
    public function index(): Studio
    {
        $mixer = Audio::getInstance()->mixer;

        return new Bounce('sample/index', [
            'title' => 'Remix example',
            'url_xml' => $mixer->uri('xml'),
            'url_json' => $mixer->uri('json'),
            'url_status' => $mixer->uri('status'),
            'url_status_with_code' => $mixer->uri('status', ['code' => 418]),
            'url_exception' => $mixer->uri('exception'),
            'url_exception_with_code' => $mixer->uri('exception', ['code' => 402]),
        ]);
    }
    // function index()

    public function text(): Studio
    {
        return Studio::factory('text', 'boo');
    }
    // function xml()

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

    public function status(Sampler $sampler): Studio
    {
        $code = $sampler->param('code', 500);

        $bounce = new Bounce('sample/status', [
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

<?php

namespace App\Channel;

use \Remix\Sampler;
use \Remix\Studio;
use \Remix\Debug;
use \Remix\Bounce;

class TopChannel extends \Remix\Channel
{
    public function index()
    {
        return 'Remix is ​​a lightweight PHP framework.';
    } // function index()

    public function bounce(Sampler $sampler) : Studio
    {
        echo $sampler->get('some');
        $message = $sampler->param('message') ?? 'hello';
        return new Bounce('test', [
            'var' => $message,
        ]);
    } // function bounce()

    public function json() : Studio
    {
        $param = [
            'some' => 'thing',
        ];
        return Studio::factory()->json($param);
    } // function json()

    public function redirect() : Studio
    {
        return Studio::factory()->redirect('top');
    }

    public function exception() : Studio
    {
        Debug::dump(__METHOD__);
        throw new \Remix\Exceptions\HttpException('exception test', 400);
        //error;
        return new Studio;
    } // function index()
} // class TopChannel

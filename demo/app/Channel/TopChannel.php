<?php

namespace App\Channel;

use \Remix\DJ;
use \Remix\Sampler;
use \Remix\Studio;
use \Remix\Monitor;
use \Remix\Bounce;
use \App\Vinyl\User;
use \App\Vinyl\Note;

class TopChannel extends \Remix\Channel
{
    public function index()
    {
        DJ::truncate('users');
        $result = User::find(1);
        return 'Remix is ​​a lightweight PHP framework.';
    } // function index()

    public function bounce(Sampler $sampler) : Studio
    {
        $message = $sampler->param('message') ?? 'hello';
        $some = $sampler->get('some') ?? '';
        $bounce = new Bounce('test');

        $bounce->var = $message;
        $bounce->some = $some;
        $bounce->escaped = '<b>boo</b>';
        $bounce->setHtml('unescaped', '<b>boo</b>');

        $bounce->arr = [1, 2, 3];

        $vinyl = Note::find(1)->turntable();
        $bounce->vinyl = $vinyl;

        return $bounce;
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
        Monitor::dump(__METHOD__);
        throw new \Remix\Exceptions\HttpException('exception test', 400);
        //error;
        return new Studio;
    } // function index()
} // class TopChannel

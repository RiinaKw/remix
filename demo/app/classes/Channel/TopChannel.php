<?php

namespace App\Channel;

use Remix\DJ;
use Remix\Sampler;
use Remix\Studio;
use Remix\Monitor;
use Remix\Bounce;
use App\Vinyl\User;
use App\Vinyl\Note;

class TopChannel extends \Remix\Channel
{
    public function index()
    {
        //DJ::table('users')->truncate();
        //$user = User::find(1);
        //var_dump($user);
        return 'Remix is ​​a lightweight PHP framework.';
    }
    // function index()

    public function bounce(Sampler $sampler): Studio
    {
        $message = $sampler->params('message') ?? 'hello';
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
    }
    // function bounce()

    public function json(): Studio
    {
        $param = [
            'some' => 'thing',
        ];
        return Studio::factory('json', $param);
    }
    // function json()

    public function redirect(): Studio
    {
        return Studio::factory()->redirect('form', [':id' => 1]);
    }
    // function redirect()

    public function exception(): Studio
    {
        throw new \Remix\Exceptions\HttpException('exception test', 400);
        //return Studio::factory();
    }
    // function exception()
}
// class TopChannel

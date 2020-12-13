<?php

namespace App\Channel;

use Remix\Sampler;
use Remix\Studio;
use Remix\Monitor;
use App\Vinyl\Note;

class ApiChannel extends \Remix\Channel
{
    public function test(Sampler $sampler): Studio
    {
        $id = $sampler->param('id');
        $ext = $sampler->param('ext') ?? 'text';
        $note = Note::find($id);

        return Studio::factory($ext, $note);
    }
    // function test()
}
// class ApiChannel

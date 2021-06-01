<?php

namespace App\Channel;

use Remix\Audio;
use Remix\Sampler;
use Remix\Studio;
use Remix\Bounce;
use Remix\Monitor;

class PostChannel extends \Remix\Channel
{
    protected function before(Sampler $sampler): Sampler
    {
        $trace = debug_backtrace()[1];
        Monitor::dump($trace['class'] . '::' . $trace['function']);
        Monitor::dump($sampler->param());
        return $sampler;
    }
    // function before()

    protected function after(Studio $studio): Studio
    {
        $mixer = Audio::getInstance()->mixer;

        $studio->url_list = $mixer->uri('post.list');
        $studio->url_new = $mixer->uri('post.new');
        $studio->url_show = $mixer->uri('post.show', [':id' => 1]);
        $studio->url_edit = $mixer->uri('post.edit', [':id' => 1]);
        $studio->url_delete = $mixer->uri('post.delete', [':id' => 1]);

        return $studio;
    }
    // function after()

    public function list(Sampler $sampler): Studio
    {
        $this->before($sampler);
        Monitor::dump(__METHOD__);

        $bounce = new Bounce('post/list');
        return $this->after($bounce);
    }
    // function list()

    public function new(Sampler $sampler): Studio
    {
        $this->before($sampler);
        Monitor::dump(__METHOD__);

        $bounce = new Bounce('post/list');
        return $this->after($bounce);
    }
    // function new()

    public function doInsert(Sampler $sampler): Studio
    {
        $this->before($sampler);
        Monitor::dump(__METHOD__);

        $bounce = new Bounce('post/list');
        return $this->after($bounce);
    }
    // function insert()

    public function show(Sampler $sampler): Studio
    {
        $this->before($sampler);
        Monitor::dump(__METHOD__);

        $bounce = new Bounce('post/list');
        return $this->after($bounce);
    }
    // function show()

    public function edit(Sampler $sampler): Studio
    {
        $this->before($sampler);
        Monitor::dump(__METHOD__);

        $bounce = new Bounce('post/list');
        return $this->after($bounce);
    }
    // function edit()

    public function doUpdate(Sampler $sampler): Studio
    {
        $this->before($sampler);
        Monitor::dump(__METHOD__);

        $bounce = new Bounce('post/list');
        return $this->after($bounce);
    }
    // function update()

    public function delete(Sampler $sampler): Studio
    {
        $this->before($sampler);
        Monitor::dump(__METHOD__);

        $bounce = new Bounce('post/list');
        return $this->after($bounce);
    }
    // function delete()

    public function doDelete(Sampler $sampler): Studio
    {
        $this->before($sampler);
        Monitor::dump(__METHOD__);

        $bounce = new Bounce('post/list');
        return $this->after($bounce);
    }
    // function destroy()

    public function confirm(Sampler $sampler): Studio
    {
        $this->before($sampler);
        Monitor::dump(__METHOD__);

        $bounce = new Bounce('post/list');
        return $this->after($bounce);
    }
    // function confirm()
}
// class ApiChannel

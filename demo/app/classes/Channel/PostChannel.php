<?php

namespace App\Channel;

use Remix\Audio;
use Remix\Sampler;
use Remix\Studio;
use Remix\Bounce;
use Remix\Monitor;

/**
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class PostChannel extends \Remix\Channel
{
    public function before(Sampler $sampler): Sampler
    {
        return $sampler;
    }
    // function before()

    public function after(Sampler $sampler, Studio $studio): Studio
    {
        $mixer = Audio::getInstance()->mixer;

        $trace = debug_backtrace()[1];
        $studio->function = $trace['class'] . '::' . $trace['function'];
        $studio->request = $sampler->method() . ' ' . $sampler->uri();
        $studio->params = $sampler->params();

        $studio->url_list = $mixer->uri('post.list');
        $studio->url_new = $mixer->uri('post.new');
        $studio->url_show = $mixer->uri('post.show', [':id' => 1]);
        $studio->url_edit = $mixer->uri('post.edit', [':id' => 1]);
        $studio->url_delete = $mixer->uri('post.delete', [':id' => 1]);
        $studio->url_validate = $mixer->uri('post.validate', [':id' => 1]);

        return $studio;
    }
    // function after()

    public function list(): Studio
    {
        $bounce = new Bounce('post/list');
        $bounce->action = 'show lists';
        return $bounce;
    }
    // function list()

    public function new(): Studio
    {
        $bounce = new Bounce('post/list');
        $bounce->action = 'new form';
        return $bounce;
    }
    // function new()

    public function doInsert(): Studio
    {
        $bounce = new Bounce('post/list');
        $bounce->action = 'insert to db';
        return $bounce;
    }
    // function insert()

    public function show(): Studio
    {
        $bounce = new Bounce('post/list');
        $bounce->action = 'item detail';
        return $bounce;
    }
    // function show()

    public function edit(): Studio
    {
        $bounce = new Bounce('post/list');
        $bounce->action = 'edit form';
        return $bounce;
    }
    // function edit()

    public function doUpdate(): Studio
    {
        $bounce = new Bounce('post/list');
        $bounce->action = 'update in db';
        return $bounce;
    }
    // function update()

    public function delete(): Studio
    {
        $bounce = new Bounce('post/list');
        $bounce->action = 'confirm delete';
        return $bounce;
    }
    // function delete()

    public function doDelete(): Studio
    {
        $bounce = new Bounce('post/list');
        $bounce->action = 'delete in db';
        return $bounce;
    }
    // function destroy()

    public function validate(): Studio
    {
        $bounce = new Bounce('post/list');
        $bounce->action = 'validate input';
        return $bounce;
    }
    // function validate()
}
// class ApiChannel

<?php

namespace RemixDemo\Channel;

use Remix\Sampler;
use Remix\Studio;
use Remix\Bounce;

class FormChannel extends \Remix\Channel
{
    public function input(): Studio
    {
        session_start();
        $form = $_SESSION['form'] ?? [];

        $bounce = new Bounce('form/input');
        $bounce->name = $form['name'] ?? '';
        $bounce->email = $form['email'] ?? '';
        return $bounce;
    }
    // function form()

    public function confirm(Sampler $sampler): Studio
    {
        session_start();
        $_SESSION['form'] = $sampler->post();

        $bounce = new Bounce('form/confirm');
        $bounce->name = $sampler->post('name', 'empty');
        $bounce->email = $sampler->post('email', 'empty');
        return $bounce;
    }
    // function confirm()

    public function submit(Sampler $sampler): Studio
    {
        session_start();
        $form = $_SESSION['form'] ?? [];
        unset($_SESSION['form']);

        $bounce = new Bounce('form/submit');
        $bounce->name = $form['name'] ?? 'empty';
        $bounce->email = $form['email'] ?? 'empty';
        return $bounce;
    }
    // function submit()
}
// class FormChannel

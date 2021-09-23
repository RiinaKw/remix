<?php

namespace Remix\Demo\Channel;

use Remix\Sampler;
use Remix\Studio;
use Remix\Bounce;
use Remix\Monitor;
use Utility\Hash;

class FormChannel extends \Remix\Channel
{
    public function input(Sampler $sampler): Studio
    {
        $session = $sampler->session();
        Monitor::dump($session);

        $bounce = new Bounce('form/input');
        $bounce->name = $session->get('form.name', '');
        $bounce->email = $session->get('form.email', '');
        return $bounce;
    }
    // function form()

    public function confirm(Sampler $sampler): Studio
    {
        $session = $sampler->session();
        $session->form = $sampler->post();
        Monitor::dump($session);

        $bounce = new Bounce('form/confirm');
        $bounce->name = $sampler->post('name', 'empty');
        $bounce->email = $sampler->post('email', 'empty');
        return $bounce;
    }
    // function confirm()

    public function submit(Sampler $sampler): Studio
    {
        $session = $sampler->session();
        $form = new Hash($session->form ?? []);
        unset($session->form);
        Monitor::dump($session);

        $bounce = new Bounce('form/submit');
        $bounce->name = $form->get('name', 'empty');
        $bounce->email = $form->get('email', 'empty');
        return $bounce;
    }
    // function submit()
}
// class FormChannel

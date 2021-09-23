<?php

namespace Remix\Demo\Channel;

use Remix\Sampler;
use Remix\Studio;
use Remix\Bounce;
use Remix\Monitor;
use Utility\Http\Session;
use Utility\Hash;

class FormChannel extends \Remix\Channel
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function input(Sampler $sampler): Studio
    {
        $session = Session::hash();
        Monitor::dump($session);

        $bounce = new Bounce('form/input');
        $bounce->name = $session->get('form.name', '');
        $bounce->email = $session->get('form.email', '');
        return $bounce;
    }
    // function form()

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function confirm(Sampler $sampler): Studio
    {
        $session = Session::hash();
        $session->form = $sampler->post();
        Monitor::dump($session);

        $bounce = new Bounce('form/confirm');
        $bounce->name = $sampler->post('name', 'empty');
        $bounce->email = $sampler->post('email', 'empty');
        return $bounce;
    }
    // function confirm()

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function submit(Sampler $sampler): Studio
    {
        $session = Session::hash();
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

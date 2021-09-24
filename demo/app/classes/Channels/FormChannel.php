<?php

namespace Remix\Demo\Channels;

use Remix\Sampler;
use Remix\Studio;
use Remix\Bounce;
use Remix\Monitor;
use Utility\Http\Session;
use Utility\Hash;
use Remix\Demo\Synthesizers\FormSynthesizer as Synthesizer;

class FormChannel extends \Remix\Channel
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function input(Sampler $sampler): Studio
    {
        $session = Session::hash();
        $form = $session->form ?: new Hash();
        Monitor::dump($session);

        $bounce = new Bounce('form/input');
        $bounce->name = $form->get('name', '');
        $bounce->email = $form->get('email', '');
        $bounce->errors = $session->errors ?: new Hash();
        return $bounce;
    }
    // function form()

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function confirm(Sampler $sampler): Studio
    {
        $synthesizer = Synthesizer::factory()->run();
        $form = $synthesizer->input();

        $session = Session::hash();
        $session->form = $form;
        $errors = $synthesizer->errors();

        if (! $errors->isEmpty()) {
            $session->errors = $errors;
            return Studio::factory()->redirect('FormInput');
        }
        unset($session->errors);
        Monitor::dump($session);

        $bounce = new Bounce('form/confirm');
        $bounce->name = $form->get('name');
        $bounce->email = $form->get('email');
        return $bounce;
    }
    // function confirm()

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function submit(Sampler $sampler): Studio
    {
        $session = Session::hash();
        $form = $session->form ?? new Hash([]);
        unset($session->form);
        Monitor::dump($session);

        $bounce = new Bounce('form/submit');
        $bounce->name = $form->get('name', '(empty)');
        $bounce->email = $form->get('email', '(empty)');
        return $bounce;
    }
    // function submit()
}
// class FormChannel

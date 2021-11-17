<?php

namespace Remix\Demo\Channels;

use Remix\Sampler;
use Remix\Studio;
use Remix\Studio\Compressor;
use Utility\Hash;
use Remix\Demo\Synthesizers\FormSynthesizer as Synthesizer;

class FormChannel extends \Remix\Channel
{
    private function csrf_check()
    {
        if ($csrf_error = $sampler->csrf_check('csrf_token')) {
            $session->errors = $csrf_error;
            return (new Studio())->redirect('FormInput');
        }
    }
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function input(Sampler $sampler): Studio
    {
        $session = $sampler->session();
        $form = $session->form ?: new Hash();

        $bounce = new Compressor('form/input');
        $bounce->name = $form->get('name', '');
        $bounce->email = $form->get('email', '');
        $bounce->errors = $session->errors ?: new Hash();

        $bounce->csrf_token = $sampler->csrf_create('csrf_token');

        return $bounce;
    }
    // function form()

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function confirm(Sampler $sampler): Studio
    {
        $session = $sampler->session();

        if ($csrf_error = $sampler->csrf_check('csrf_token')) {
            $session->errors = $csrf_error;
            return (new Studio())->redirect('FormInput');
        }

        $synthesizer = $sampler->synthesize(Synthesizer::class);
        $form = $synthesizer->input();

        $session->form = $form;
        $errors = $synthesizer->errors();

        if (! $errors->isEmpty()) {
            $session->errors = $errors;
            return (new Studio())->redirect('FormInput');
        }
        unset($session->errors);

        $bounce = new Compressor('form/confirm');
        $bounce->name = $form->get('name');
        $bounce->email = $form->get('email');

        $bounce->csrf_token = $sampler->csrf_create('csrf_token');

        return $bounce;
    }
    // function confirm()

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function submit(Sampler $sampler): Studio
    {
        $session = $sampler->session();

        if ($csrf_error = $sampler->csrf_check('csrf_token')) {
            $session->errors = $csrf_error;
            return (new Studio())->redirect('FormInput');
        }

        $form = $session->form ?? new Hash([]);
        unset($session->form);

        $bounce = new Compressor('form/submit');
        $bounce->name = $form->get('name', '(empty)');
        $bounce->email = $form->get('email', '(empty)');
        return $bounce;
    }
    // function submit()
}
// class FormChannel

<?php

namespace Remix\Demo\Channels;

use Remix\Sampler;
use Remix\Studio;
use Remix\Studio\Compressor;
use Utility\Hash;
use Utility\Http\Csrf;
use Remix\Demo\Synthesizers\FormSynthesizer as Synthesizer;

class FormChannel extends \Remix\Channel
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function input(Sampler $sampler): Studio
    {
        $session = $sampler->session();
        $form = $session->form ?: new Hash();

        $bounce = new Compressor('form/input');
        $bounce->form = $form;
        $bounce->errors = $session->errors ?: new Hash();

        $bounce->csrf = Csrf::factory();

        return $bounce;
    }
    // function form()

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function confirm(Sampler $sampler): Studio
    {
        if (! Csrf::check()) {
            return (new Studio())->redirect('FormInput');
        }

        $session = $sampler->session();
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
        $bounce->form = $form;

        $bounce->csrf = Csrf::factory();

        return $bounce;
    }
    // function confirm()

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function submit(Sampler $sampler): Studio
    {
        if (! Csrf::check()) {
            return (new Studio())->redirect('FormInput');
        }

        $session = $sampler->session();
        $form = $session->form ?? new Hash([]);
        unset($session->form);

        $bounce = new Compressor('form/submit');
        $bounce->form = $form;
        return $bounce;
    }
    // function submit()
}
// class FormChannel

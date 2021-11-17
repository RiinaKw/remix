<?php

namespace Remix\DemoTests;

use Utility\Tests\WebTestCase;
use Utility\Http\Session;
use Utility\Http\Csrf;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class FormTest extends WebTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->initialize(__DIR__ . '/../../../demo/app');
    }

    /**
     * @runInSeparateProcess
     */
    public function testInput(): void
    {
        $this->get('/form/input');
        $this->assertHtmlContains('Input form');
    }

    /**
     * @runInSeparateProcess
     */
    public function test405Confirm(): void
    {
        $this->get('/form/confirm');
        $this->assertStatusCode(405);
    }

    /**
     * @runInSeparateProcess
     */
    public function testCsrfConfirm(): void
    {
        $this->get('/form/input');

        $this->post('/form/confirm');
        $this->assertStatusCode(303);
        $this->assertRedirectUri('http://remix.test/form/input');

        $this->assertSame('Illegal screen transition', Session::hash()->sess_csrf_error);
    }

    /**
     * @runInSeparateProcess
     */
    public function testEmptyConfirm(): void
    {
        $this->get('/form/input');

        $this->post('/form/confirm', [
            'csrf_token' => Csrf::token(),
        ]);
        $this->assertStatusCode(303);
        $this->assertRedirectUri('http://remix.test/form/input');

        $session_errors = Session::hash()->errors;
        $this->assertSame('name : your name is required', $session_errors->name);
        $this->assertSame('email : your mail address is required', $session_errors->email);
    }

    /**
     * @runInSeparateProcess
     */
    public function testLongName(): void
    {
        $this->get('/form/input');

        $this->post('/form/confirm', [
            'name' => 'boooooooo',
            'csrf_token' => Csrf::token(),
        ]);
        $this->assertStatusCode(303);

        $session_errors = Session::hash()->errors;
        $this->assertSame('your name must be 5 characters or less', $session_errors->name);
    }

    /**
     * @runInSeparateProcess
     */
    public function testMalformedEmail(): void
    {
        $this->get('/form/input');

        $this->post('/form/confirm', [
            'email' => 'malformed',
            'csrf_token' => Csrf::token(),
        ]);
        $this->assertStatusCode(303);

        $session_errors = Session::hash()->errors;
        $this->assertSame('your mail address is invalid email address', $session_errors->email);
    }

    /**
     * @runInSeparateProcess
     */
    public function testValidConfirm(): void
    {
        $this->get('/form/input');

        $this->post('/form/confirm', [
            'name' => 'Riina',
            'email' => 'riinak.tv@gmail.com',
            'csrf_token' => Csrf::token(),
        ]);
        $this->assertStatusCode(200);

        $this->assertNull(Session::hash()->errors);

        $session_params = Session::hash()->form;
        $this->assertSame('Riina', $session_params->name);
        $this->assertSame('riinak.tv@gmail.com', $session_params->email);
    }

    /**
     * @runInSeparateProcess
     */
    public function testReloadConfirm(): void
    {
        $this->get('/form/input');

        $this->post('/form/confirm', [
            'name' => 'Riina',
            'email' => 'riinak.tv@gmail.com',
            'csrf_token' => Csrf::token(),
        ]);

        $this->reload();
        $this->assertStatusCode(303);
        $this->assertRedirectUri('http://remix.test/form/input');
        $this->assertSame('Illegal screen transition', Session::hash()->sess_csrf_error);
    }

    /**
     * @runInSeparateProcess
     */
    public function test405Submit(): void
    {
        $this->get('/form/submit');
        $this->assertStatusCode(405);
    }

    /**
     * @runInSeparateProcess
     */
    public function testValidSubmit(): void
    {
        $this->get('/form/input');

        $this->post('/form/confirm', [
            'name' => 'Riina',
            'email' => 'riinak.tv@gmail.com',
            'csrf_token' => Csrf::token(),
        ]);

        $this->post('/form/submit', [
            'csrf_token' => Csrf::token(),
        ]);
        $this->assertStatusCode(200);

        $this->assertHtmlContains('<dd>Riina</dd>');
        $this->assertHtmlContains('<dd>riinak.tv@gmail.com</dd>');
    }

    /**
     * @runInSeparateProcess
     */
    public function testDoubleSubmit(): void
    {
        $this->get('/form/input');

        $this->post('/form/confirm', [
            'name' => 'Riina',
            'email' => 'riinak.tv@gmail.com',
            'csrf_token' => Csrf::token(),
        ]);

        $this->post('/form/submit', [
            'csrf_token' => Csrf::token(),
        ]);

        $this->reload();
        $this->assertStatusCode(303);
        $this->assertRedirectUri('http://remix.test/form/input');
        $this->assertSame('Illegal screen transition', Session::hash()->sess_csrf_error);
    }
}

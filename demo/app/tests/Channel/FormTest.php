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

        // Be sure to point to the app directory
        $this->initialize(__DIR__ . '/../..');
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
        $this->assertStatusCode(307);
        $this->assertRedirectUri('http://remix.test/form/input');

        $this->assertSame('Illegal screen transition', Session::hash()->sess_csrf_error);
    }

    /**
     * @runInSeparateProcess
     */
    public function testEmptyConfirm(): void
    {
        $this->get('/form/input');

        $this->post('/form/confirm', Csrf::post());
        $this->assertStatusCode(307);
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

        $params = [
            'name' => str_repeat('x', 50),
        ];
        $params += Csrf::post();
        $this->post('/form/confirm', $params);
        $this->assertStatusCode(307);
        $this->assertRedirectUri('http://remix.test/form/input');

        $session_errors = Session::hash()->errors;
        $this->assertSame('your name must be 20 characters or less', $session_errors->name);
    }

    /**
     * @runInSeparateProcess
     */
    public function testMalformedEmail(): void
    {
        $this->get('/form/input');

        $params = [
            'email' => 'malformed',
        ];
        $params += Csrf::post();
        $this->post('/form/confirm', $params);
        $this->assertStatusCode(307);
        $this->assertRedirectUri('http://remix.test/form/input');

        $session_errors = Session::hash()->errors;
        $this->assertSame('your mail address is invalid email address', $session_errors->email);
    }

    /**
     * @runInSeparateProcess
     */
    public function testValidConfirm(): void
    {
        $this->get('/form/input');

        $params = [
            'name' => 'Riina',
            'email' => 'riinak.tv@gmail.com',
        ];
        $params += Csrf::post();
        $this->post('/form/confirm', $params);
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

        $params = [
            'name' => 'Riina',
            'email' => 'riinak.tv@gmail.com',
        ];
        $params += Csrf::post();
        $this->post('/form/confirm', $params);

        $this->reload();
        $this->assertStatusCode(307);
        $this->assertRedirectUri('http://remix.test/form/input');
        $this->assertSame('Illegal screen transition', Session::hash()->sess_csrf_error);
    }

    /**
     * @runInSeparateProcess
     */
    public function testBackToForm(): void
    {
        $this->get('/form/input');

        $params = [
            'name' => 'Riina',
            'email' => 'riinak.tv@gmail.com',
        ];
        $params += Csrf::post();
        $this->post('/form/confirm', $params);

        $this->post('/form/input');

        $session_params = Session::hash()->form;
        $this->assertSame('Riina', $session_params->name);
        $this->assertSame('riinak.tv@gmail.com', $session_params->email);
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

        $params = [
            'name' => 'Riina',
            'email' => 'riinak.tv@gmail.com',
        ];
        $params += Csrf::post();
        $this->post('/form/confirm', $params);

        $this->post('/form/submit', Csrf::post());
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

        $params = [
            'name' => 'Riina',
            'email' => 'riinak.tv@gmail.com',
        ];
        $params += Csrf::post();
        $this->post('/form/confirm', $params);

        $this->post('/form/submit', Csrf::post());

        $this->reload();
        $this->assertStatusCode(307);
        $this->assertRedirectUri('http://remix.test/form/input');
        $this->assertSame('Illegal screen transition', Session::hash()->sess_csrf_error);
    }
}

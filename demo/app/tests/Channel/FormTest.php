<?php

namespace RemixDemo\Tests;

use RemixDemo\TestCase\WebTestCase as TestCase;
// Traits
use Utility\Tests\Traits;
// Utility
use Utility\Http\Session;
use Utility\Http\Csrf;

/**
 * Test of FormChannel in the demo env.
 * @package  Demo\TestCase\Channels
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class FormTest extends TestCase
{
    use Traits\Html;
    use Traits\Redirect;

    /**
     * Does the input form contain the required HTML?
     *
     * @runInSeparateProcess
     */
    public function testInput(): void
    {
        // Try to input
        $this->get('/form/input');

        // Is valid response?
        $this->assertStatusCode(200);
        $this->assertMimeType('text/html');

        // Is it valid HTML?
        $this->assertHtmlContains('Input form');
        $this->assertInputText('name', '');
        $this->assertInputText('email', '');
        $this->assertTextarea('profile', '');
    }

    /**
     * Does it give an error when accessing GET form/confirm?
     *
     * @runInSeparateProcess
     */
    public function testDirectConfirm(): void
    {
        // Will GET denied?
        $this->get('/form/confirm');
        $this->assertStatusCode(405);
    }

    /**
     * Does it give an error when accessing GET form/submit?
     *
     * @runInSeparateProcess
     */
    public function testDirectSubmit(): void
    {
        // Will GET denied?
        $this->get('/form/submit');
        $this->assertStatusCode(405);
    }

    /**
     * Does it give a CSRF error on POST form/confirm without a token?
     *
     * @runInSeparateProcess
     */
    public function testConfirmWithoutCsrf(): void
    {
        // Set up the CSRF token with GET form/input
        $this->get('/form/input');

        // Try to confirm without CSRF
        $this->post('/form/confirm');

        // Will CSRF error occurs?
        $this->assertStatusCode(307);
        $this->assertRedirectPath('/form/input');
        $this->assertSame('Illegal screen transition', Session::hash()->sess_csrf_error);
    }

    /**
     * Does it give a CSRF error on POST form/submit without a token?
     *
     * @runInSeparateProcess
     */
    public function testSubmitWithoutCsrf(): void
    {
        // Set up the CSRF token with GET form/input
        $this->get('/form/input');

        // Try to submit without CSRF
        $this->post('/form/submit');

        // Will CSRF error occurs?
        $this->assertStatusCode(307);
        $this->assertRedirectPath('/form/input');
        $this->assertSame('Illegal screen transition', Session::hash()->sess_csrf_error);
    }

    /**
     * Does it give an error if it doesn't fill in the required fields?
     *
     * @runInSeparateProcess
     */
    public function testEmptyConfirm(): void
    {
        // Set up the CSRF token with GET form/input
        $this->get('/form/input');

        // Try to confirm without inputs
        $params = Csrf::post();
        $this->post('/form/confirm', $params);

        // Does it give an input error?
        $this->assertStatusCode(307);
        $this->assertRedirectPath('/form/input');

        // Is the error message correct?
        $session_errors = Session::hash()->errors;
        $this->assertSame('name : your name is required', $session_errors->name);
        $this->assertSame('email : your mail address is required', $session_errors->email);
    }

    /**
     * Does it give an error if the name is too long?
     *
     * @runInSeparateProcess
     */
    public function testLongName(): void
    {
        // Set up the CSRF token with GET form/input
        $this->get('/form/input');

        // Set up the input parameters with long name
        $long_name = str_repeat('x', 50);
        $params = [
            'name' => $long_name,
        ];
        $params += Csrf::post();

        // Try to confirm
        $this->post('/form/confirm', $params);

        // Does it give an input error?
        $this->assertStatusCode(307);
        $this->assertRedirectPath('/form/input');

        // Is the error message correct?
        $session_errors = Session::hash()->errors;
        $this->assertSame('your name must be 20 characters or less', $session_errors->name);

        // Try to return to the input
        $this->post('/form/input');

        // Is the input tag set correctly?
        $this->assertInputText('name', $long_name);
    }

    /**
     * Does it give an error if the email is malformed?
     *
     * @runInSeparateProcess
     */
    public function testMalformedEmail(): void
    {
        // Set up the CSRF token with GET form/input
        $this->get('/form/input');

        // Set up the input parameters with malformed email
        $params = [
            'email' => 'malformed',
        ];
        $params += Csrf::post();

        // Try to confirm
        $this->post('/form/confirm', $params);

        // Does it give an input error?
        $this->assertStatusCode(307);
        $this->assertRedirectPath('/form/input');

        // Is the error message correct?
        $session_errors = Session::hash()->errors;
        $this->assertSame('your mail address is invalid email address', $session_errors->email);

        // Try to return to the input
        $this->post('/form/input');

        // Is the input tag set correctly?
        $this->assertInputText('email', 'malformed');
    }

    /**
     * Is a input/confirm displayed if the input is valid?
     *
     * @runInSeparateProcess
     */
    public function testValidConfirm(): void
    {
        // Set up the CSRF token with GET form/input
        $this->get('/form/input');

        // Set up the input parameters
        $params = [
            'name' => 'Riina',
            'email' => 'riinak.tv@gmail.com',
            'profile' => 'Am I born in Tatooine...?',
        ];
        $params += Csrf::post();

        // Try to confirm with valid input
        $this->post('/form/confirm', $params);

        // Will the confirm process succeed?
        $this->assertStatusCode(200);
        $this->assertMimeType('text/html');
        $this->assertNull(Session::hash()->errors);

        // Is it properly registered in the session?
        $session_params = Session::hash()->form;
        $this->assertSame('Riina', $session_params->name);
        $this->assertSame('riinak.tv@gmail.com', $session_params->email);
        $this->assertSame('Am I born in Tatooine...?', $session_params->profile);
    }

    /**
     * Does it give a CSRF error when form/confirm is reloaded?
     *
     * @runInSeparateProcess
     */
    public function testReloadConfirm(): void
    {
        // Set up the CSRF token with GET form/input
        $this->get('/form/input');

        // Set up the input parameters
        $params = [
            'name' => 'Riina',
            'email' => 'riinak.tv@gmail.com',
            'profile' => 'Am I born in Tatooine...?',
        ];
        $params += Csrf::post();

        // Do confirm
        $this->post('/form/confirm', $params);

        // Try to reload
        $this->reload();

        // Will CSRF error occurs?
        $this->assertStatusCode(307);
        $this->assertRedirectPath('/form/input');
        $this->assertSame('Illegal screen transition', Session::hash()->sess_csrf_error);
    }

    /**
     * Is it possible to return to the form/input correctly from the form/confirm?
     *
     * @runInSeparateProcess
     */
    public function testBackToForm(): void
    {
        // Set up the CSRF token with GET form/input
        $this->get('/form/input');

        // Set up the input parameters
        $params = [
            'name' => 'Riina',
            'email' => 'riinak.tv@gmail.com',
            'profile' => 'Am I born in Tatooine...?',
        ];
        $params += Csrf::post();

        // Do confirm
        $this->post('/form/confirm', $params);

        // Try to POST form/input, the input should be left
        $this->post('/form/input');
        $this->assertStatusCode(200);
        $this->assertMimeType('text/html');

        // Is the session left by POST input/form?
        $this->assertNotNull(Session::hash()->form);

        // Is the input tag set correctly by POST input/form?
        $this->assertInputText('name', 'Riina');
        $this->assertInputText('email', 'riinak.tv@gmail.com');
        $this->assertTextarea('profile', 'Am I born in Tatooine...?');
    }

    /**
     * Will GET form/input delete the form data?
     *
     * @runInSeparateProcess
     */
    public function testResetForm(): void
    {
        // Set up the CSRF token with GET form/input
        $this->get('/form/input');

        // Set up the input parameters
        $params = [
            'name' => 'Riina',
            'email' => 'riinak.tv@gmail.com',
            'profile' => 'Am I born in Tatooine...?',
        ];
        $params += Csrf::post();

        // Do confirm
        $this->post('/form/confirm', $params);

        // Try to GET form/input, the input should be deleted
        $this->get('/form/input');
        $this->assertStatusCode(200);
        $this->assertMimeType('text/html');

        // Is the session empty by GET input/form?
        $this->assertNull(Session::hash()->form);

        // Is the input tag empty by GET input/form?
        $this->assertInputText('name', '');
        $this->assertInputText('email', '');
        $this->assertTextarea('profile', '');
    }

    /**
     * Will the form/submit succeed if the input is valid?
     *
     * @runInSeparateProcess
     */
    public function testValidSubmit(): void
    {
        // Set up the CSRF token with GET form/input
        $this->get('/form/input');

        // Set up the input parameters
        $params = [
            'name' => 'Riina',
            'email' => 'riinak.tv@gmail.com',
            'profile' => 'Am I born in Tatooine...?',
        ];
        $params += Csrf::post();

        // Do confirm
        $this->post('/form/confirm', $params);

        // Try to submit
        $this->post('/form/submit', Csrf::post());

        // Will the submit process succeed?
        $this->assertStatusCode(200);
        $this->assertMimeType('text/html');

        // Is it valid HTML?
        $this->assertHtmlContains('<dd>Riina</dd>');
        $this->assertHtmlContains('<dd>riinak.tv@gmail.com</dd>');
        $this->assertHtmlContains('<dd>Am I born in Tatooine...?</dd>');
    }

    /**
     * Does it give a CSRF error when form/submit is reloaded?
     *
     * @runInSeparateProcess
     */
    public function testReloadSubmit(): void
    {
        $this->get('/form/input');

        $params = [
            'name' => 'Riina',
            'email' => 'riinak.tv@gmail.com',
            'profile' => 'Am I born in Tatooine...?',
        ];
        $params += Csrf::post();

        // Do confirm
        $this->post('/form/confirm', $params);

        // Do submit
        $this->post('/form/submit', Csrf::post());

        // Try to reload
        $this->reload();

        // Will CSRF error occurs?
        $this->assertStatusCode(307);
        $this->assertRedirectPath('/form/input');
        $this->assertSame('Illegal screen transition', Session::hash()->sess_csrf_error);
    }
}

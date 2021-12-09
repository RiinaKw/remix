<?php

namespace Remix\CoreTests;

use PHPUnit\Framework\TestCase;
// Target of the test
use Remix\Synthesizer;
// Remix core
use Remix\Filter;
// Utility
use Utility\Http\PostHash;

class SynthesizerTest extends TestCase
{
    use \Utility\Tests\InvokePrivateBehavior;

    protected $synthesizer = null;

    protected function setUp(): void
    {
        $this->post_hash = PostHash::factory();
        $this->synthesizer = new Synthesizer();

        $this->invokePropertyValue(
            $this->synthesizer,
            'filters',
            [
                Filter::define('name', 'your name')->rules('required|max:5'),
                Filter::define('email', 'your mail address')->rules('required|email'),
            ]
        );
    }

    public function tearDown(): void
    {
        unset($this->synthesizer);
    }

    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function testValid(): void
    {
        // has it passed the validation?
        $_POST['name'] = 'riina';
        $_POST['email'] = 'riina@example.net';
        $this->synthesizer->run($this->post_hash);

        $expectedInput = [
            'name' => 'riina',
            'email' => 'riina@example.net',
        ];
        $expectedErrors = [];
        $this->assertSame($expectedInput, $this->synthesizer->input()->get());
        $this->assertSame($expectedErrors, $this->synthesizer->errors()->get());
    }

    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function testRequired(): void
    {
        // hasn't it passed the validation?
        unset($_POST['name']);
        unset($_POST['email']);
        $this->synthesizer->run($this->post_hash);

        $expectedInput = [
            'name' => '',
            'email' => '',
        ];
        $expectedErrors = [
            'name' => 'name : your name is required',
            'email' => 'email : your mail address is required',
        ];
        $this->assertSame($expectedInput, $this->synthesizer->input()->get());
        $this->assertSame($expectedErrors, $this->synthesizer->errors()->get());
    }

    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function testInvalid(): void
    {
        // is the error message correct?
        $_POST['name'] = 'riina kwaad';
        $_POST['email'] = 'informal address';
        $this->synthesizer->run($this->post_hash);

        $expectedErrors = [
            'name' => 'your name must be 5 characters or less',
            'email' => 'your mail address is invalid email address',
        ];
        $this->assertSame($expectedErrors, $this->synthesizer->errors()->get());
    }
}
// class MixerTest
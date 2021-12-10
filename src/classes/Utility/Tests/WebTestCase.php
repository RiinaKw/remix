<?php

namespace Utility\Tests;

use Utility\Tests\DemoTestCase;
use Remix\Audio;
use Remix\Reverb;
use Remix\Lyric;
use Remix\Exceptions\HttpException;

abstract class WebTestCase extends DemoTestCase
{
    use \Utility\Tests\InvokePrivateBehavior;

    protected $studio = null;
    protected $html = null;

    protected $prev_path = '';
    protected $prev_method = '';
    protected $prev_post = [];

    protected function initialize(string $app_dir)
    {
        Audio::isDebug();

        // Turn off the CLI flag
        $this->invokePropertyValue(Audio::getInstance(), 'is_cli', false);

        $this->daw->initialize($app_dir);
        chdir($app_dir . '/..');
    }

    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function __get(string $key)
    {
        switch ($key) {
            case 'METHOD':
                return $_SERVER['REQUEST_METHOD'];
            case 'POST':
                return $_POST;
        }
        return null;
    }

    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function __set(string $key, $value)
    {
        switch ($key) {
            case 'PATH':
                $_SERVER['PATH_INFO'] = $value;
                break;
            case 'METHOD':
                $_SERVER['REQUEST_METHOD'] = $value;
                break;
            case 'POST':
                $_POST = $value;
                break;
        }
    }

    private function request(string $path)
    {
        $this->prev_path = $path;
        $this->prev_method = $this->METHOD;
        $this->prev_post = $this->POST;
        try {
            $this->PATH = $path;
            $this->daw->playWeb();
            $reverb = $this->invokeProperty($this->daw, 'reverb');
        } catch (HttpException $e) {
            $reverb = Reverb::exeption($e, Audio::getInstance()->preset);
        }
        $this->studio = $this->invokeProperty($reverb, 'studio');
        $this->html = $this->studio->recorded();
        $this->studio->sendHeader();
    }

    protected function reload()
    {
        $this->METHOD = $this->prev_method;
        $this->POST = $this->prev_post;
        $this->request($this->prev_path);
    }

    protected function get(string $path)
    {
        $this->METHOD = 'GET';
        $this->request($path);
    }

    protected function post(string $path, array $post = [])
    {
        $this->METHOD = 'POST';
        $this->POST = $post;
        $this->request($path);
    }

    protected function assertHtmlContains(string $text)
    {
        $this->assertNotFalse(strpos($this->html, $text), "The HTML does not contain '{$text}'");
    }

    protected function assertStatusCode(int $code)
    {
        $this->assertSame($code, $this->studio->getStatusCode());
    }

    protected function assertMimeType(string $mime)
    {
        $this->assertSame($mime, $this->studio->getMimeType());
    }

    protected function assertRedirectUri(string $uri): void
    {
        $this->assertSame($uri, $this->studio->getRedirectUri());
    }

    protected function assertRedirectPath(string $path): void
    {
        $uri = Lyric::getInstance()->sing($path);
        $this->assertRedirectUri($uri);
    }

    protected function assertRedirectName(string $name): void
    {
        $uri = Lyric::getInstance()->named($name);
        $this->assertRedirectUri($uri);
    }

    /**
     * Get the attribute list of the HTML tag
     *
     * @param  string $tag            tag string
     * @return array<string, string>  An array of the form [name => value]
     */
    protected function getHtmlAttributes(string $tag): array
    {
        // Cut out the format of attribute="value"
        preg_match_all('/(?<name>[a-zA-Z\-_]+)="(?<value>.*?)"/', $tag, $matches);

        // Callback for the cut out attributes and values
        $attrs = [];
        array_map(
            function (string $name, string $value) use (&$attrs) {
                // Add to attribute list
                $attrs[$name] = $value;
            },
            $matches['name'],
            $matches['value']
        );
        return $attrs;
    }
    // function getHtmlAttributes()

    /**
     * Search for empty tag ( <tagname ... /> )
     *
     * @param  string $tagname     Target tag name
     * @param  string $attr_name   Attribute name for unique identification
     * @param  string $attr_value  Attribute value for unique identification
     * @return array|null          List of attributes if found、null if not found
     */
    protected function getEmptyTag(string $tagname, string $attr_name, string $attr_value): ?array
    {
        $matches = null;

        // Search by tag string
        preg_match_all(
            "/<{$tagname}(\s+.*?|)\/?>/s",
            $this->html,
            $matches
        );

        // Loop the hit tag
        foreach ($matches[0] as $tag) {
            // Get the attribute list
            $attrs = $this->getHtmlAttributes($tag);

            // If the specified attribute has the specified value, return the tag
            if ($attrs[$attr_name] === $attr_value) {
                return $attrs;
            }
        }
        // Not found
        return null;
    }
    // function getEmptyTag()

    /**
     * Search for normal tag ( <tagname ...> ... </tagname> )
     *
     * @param  string $tagname     Target tag name
     * @param  string $attr_name   Attribute name for unique identification
     * @param  string $attr_value  Attribute value for unique identification
     * @return array|null          List of attributes if found、null if not found
     */
    protected function getOpenTag(string $tagname, string $attr_name, string $attr_value): ?array
    {
        // Search by tag string
        preg_match_all(
            "/<{$tagname}(\s+.*?>|>)(?<content>.*?)<\/{$tagname}>/s",
            $this->html,
            $matches
        );

        // Loop the hit tag
        foreach ($matches[0] as $idx => $tag) {
            // Get the attribute list and add the content
            $attrs = [
                'content' => $matches['content'][$idx],
            ];
            $attrs += $this->getHtmlAttributes($tag);

            // If the specified attribute has the specified value, return the tag
            if ($attrs[$attr_name] === $attr_value) {
                return $attrs;
            }
        }
        // Not found
        return null;
    }
    // function getOpenTag()

    /**
     * Is <input> included correctly?
     *
     * @param  string $type   Vaule of "type" attribute
     * @param  string $name   Value of "name" attribute
     * @param  string $value  Value of "value" attribute
     */
    protected function assertInput(string $type, string $name, string $value): void
    {
        $this->assertTrue($this->html !== '', 'HTML is empty');

        $tagname = 'input';
        $attrs = $this->getEmptyTag($tagname, 'name', $name);

        $test = [
            'type' => $type,
            'value' => $value,
        ];
        foreach ($test as $name => $value) {
            $this->assertTrue(isset($attrs[$name]));
            $this->assertSame($value, $attrs[$name]);
        }
    }
    // function assertInput()

    /**
     * Is <input type="text"> included correctly?
     *
     * @param  string $name   Value of "name" attribute
     * @param  string $value  Value of "value" attribute
     */
    protected function assertInputText(string $name, string $value): void
    {
        $this->assertInput('text', $name, $value);
    }

    /**
     * Is <textarea> included correctly?
     *
     * @param  string $name   Value of "name" attribute
     * @param  string $value  String in textarea
     */
    protected function assertTextarea(string $name, string $value): void
    {
        $this->assertTrue($this->html !== '', 'HTML is empty');

        $tagname = 'textarea';
        $attrs = $this->getOpenTag($tagname, 'name', $name);

        $test = [
            'name' => $attrs['name'],
            'content' => $value,
        ];
        foreach ($test as $name => $value) {
            $this->assertTrue(isset($attrs[$name]));
            $this->assertSame($value, $attrs[$name]);
        }
    }
    // function assertTextarea()
}

<?php

namespace Utility\Tests\Traits;

/**
 * PHPUnit TestCase trait for HTML operations.
 * @package  TestCase\Traits
 */
trait Html
{
    /**
     * @property string $htmls
     */

    /**
     * Is the output HTML?
     */
    protected function assertHtml(): void
    {
        $this->assertTrue($this->html !== '', 'HTML is empty');
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
        foreach ($matches[0] as $idx => $tag) {
            // Get the attribute list
            $info = [
                'html' => $matches[0][$idx],
                'attrs' => $this->getHtmlAttributes($tag),
            ];

            // If the specified attribute has the specified value, return the tag
            if ($info['attrs'][$attr_name] === $attr_value) {
                return $info;
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
            $info = [
                'html' => $matches[0][$idx],
                'content' => $matches['content'][$idx],
                'attrs' => $this->getHtmlAttributes($tag),
            ];

            // If the specified attribute has the specified value, return the tag
            if ($info['attrs'][$attr_name] === $attr_value) {
                return $info;
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
        $this->assertHtml();

        $tagname = 'input';
        $info = $this->getEmptyTag($tagname, 'name', $name);

        $this->assertIsArray($info, "No matching any HTML tags");

        $test = [
            'type' => $type,
            'value' => $value,
        ];
        $html = $info['html'] ?? '';
        $attrs = $info['attrs'] ?? [];

        foreach ($test as $attr => $value) {
            $this->assertArrayHasKey(
                $attr,
                $attrs,
                "HTML {$html} does not contain attr '{$attr}'"
            );
            $this->assertSame(
                $value,
                $attrs[$attr],
                "HTML {$html} does not contain value '{$value}' in the attr '{$attr}'"
            );
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
        $this->assertHtml();

        $tagname = 'textarea';
        $info = $this->getOpenTag($tagname, 'name', $name);

        $this->assertIsArray($info, "No matching any HTML tags");

        $test = [
            'name' => $name,
        ];
        $html = $info['html'] ?? '';
        $attrs = $info['attrs'] ?? [];

        $this->assertSame($value, $info['content'], "HTML {$info['html']} body does not contain string '{$value}'");

        foreach ($test as $attr => $value) {
            $this->assertArrayHasKey(
                $attr,
                $attrs,
                "HTML {$html} does not contain attr '{$attr}'"
            );
            $this->assertSame(
                $value,
                $attrs[$attr],
                "HTML {$html} does not contain value '{$value}' in the attr '{$attr}'"
            );
        }
    }
    // function assertTextarea()
}
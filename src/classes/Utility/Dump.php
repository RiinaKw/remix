<?php

namespace Utility;

use Exception;

/**
 * Utilities of dumping variables
 *
 * @package  Utility\Dump
 * @todo Write the details.
 */
class Dump
{
    //private static $instance = null;

    private $configs = [
        'id_prefix' => '',
        'doccoment' => false,
    ];

    private $html_id = null;

    private const STYLES = <<<STYLES
{{html_id}} {
    display: block;
    padding: 1em;
    background-color: #eeeeee;
}
{{html_id}} pre {
    margin: 0;
}
{{html_id}} details {
    margin-left: 2em;
}
{{html_id}} summary {
    cursor: pointer;
}
{{html_id}} ol {
    list-style-type: none;
    margin: 0;
    padding: 0 0 0 1em;
}
{{html_id}} li {
    padding: 0.2em;
}
{{html_id}} php-type {
    font-style: normal;
    font-size: 80%;
    color: #000099;
}
{{html_id}} php-key {
    font-size: 80%;
    color: #cc6600;
}
{{html_id}} php-key:after {
    content: "=>";
    margin: 0 1em;
}
{{html_id}} php-value {
    font-weight: bold;
    font-style: normal;
}
{{html_id}} php-scope {
    font-size: 80%;
    color: #666666;
}
STYLES;

    private function __construct(array $configs)
    {
        $this->configs = array_merge($this->configs, $configs);
        $this->html_id =
            $this->configs['id_prefix']
            ? $this->configs['id_prefix'] . mt_rand()
            : '';
    }

    public static function output($object, array $config = []): void
    {
        echo static::html($object, $config);
    }

    public static function html($object, array $config = []): string
    {
        $instance = new static($config);
        $html = preg_replace('/^<details>/', "<details open>", $instance->internal($object));

        if ($instance->html_id) {
            $styles =
                "\n<style>\n" .
                str_replace('{{html_id}}', '#' . $instance->html_id, static::STYLES) .
                "\n</style>\n";

            return "<code id=\"{$instance->html_id}\">{$html}</code>" . $styles;
        } else {
            return $html;
        }
    }
    // function html()

    private function internal(&$object): string
    {
        switch (gettype($object)) {
            case 'array':
                return $this->array($object);

            case 'object':
                return $this->object($object);

            case 'string':
                return $this->string($object);

            case 'resource':
                return $this->resource($object);

            case 'NULL':
                return $this->null();

            default:
                return $this->number($object);
        }
    }
    // function internal()

    private function null(): string
    {
        return Html::varHtml('null');
    }
    // function null()

    private function resource(&$object): string
    {
        $type = get_resource_type($object);
        return Html::varHtml("resource ({$type})");
    }
    // function resource()

    private function number(&$object): string
    {
        $type = gettype($object);
        switch ($type) {
            case 'integer':
            case 'double':
                break;

            case 'boolean':
                $object = ($object ? 'true' : 'false');
                break;

            default:
                throw new Exception("unknown type {$type}");
            break;
        }

        return Html::varHtml($type, Html::valueHtml($object));
    }
    // function number()

    private function string(string &$object, bool $is_pre = false): string
    {
        $length = strlen($object);
        $label = Html::quote("string($length)");

        if (strpos($object, "\n") !== false) {
            $is_pre = true;
        }

        if ($is_pre) {
            $content = '<pre>' . Html::valueHtml($object) . '</pre>';
        } else {
            $content = Html::quote($object);
            $content = Html::valueHtml($content);
        }
        return Html::varHtml($label, $content);
    }
    // function string()

    private function array(array &$object): string
    {

        $html = '';
        foreach ($object as $key => $value) {
            $result = $this->internal($value);
            if (is_string($key)) {
                $key = Html::quote($key);
            }

            $html .= Html::liHtml($key, $result);
        }

        $length = count($object);
        return Html::detailsHtml(
            Html::typeHtml("array({$length})"),
            $html
        );
    }
    // function array()

    private function object(object &$object): string
    {
        return DumpObject::html($object, $this->configs);
    }
    // function object()
}
// class VarDumpToHTML

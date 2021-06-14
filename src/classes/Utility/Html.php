<?php

namespace Utility;

class Html
{
    public static function quote(string $string): string
    {
        return "\"{$string}\"";
    }
    // function quote()

    public function typeHtml(string $value): string
    {
        return "<php-type>{$value}</php-type>";
    }
    // function typeHtml()

    public function keyHtml(string $value): string
    {
        return "<php-key>{$value}</php-key>";
    }
    // function valueHtml()

    public function valueHtml(string $value): string
    {
        return "<php-value>{$value}</php-value>";
    }
    // function valueHtml()

    public function scopeHtml(string $value): string
    {
        return "<php-scope>{$value}</php-scope>";
    }
    // function scopeHtml()

    public static function varHtml(string $type, ?string $value = null): string
    {
        return
            "<var>\n" .
            "    <php-type>{$type}</php-type>\n" .
            "    {$value}\n" .
            "</var>" . "\n";
    }
    // function varHtml()

    public static function detailsHtml(string $summary, string $content, bool $is_open = false): string
    {
        $tag = $is_open ? '<details open>' : '<details>';
        return
            "{$tag}\n" .
            "    <summary>\n" .
            "        {$summary}\n" .
            "    </summary>\n" .
            "    <ol>{$content}</ol>\n" .
            "</details>";
    }
    // function detailsHtml()

    public static function liHtml(string $key, ?string $content = null, ?string $scope = null): string
    {
        $key = static::keyHtml($key);
        $scope = $scope ? static::scopeHtml($scope) : '';
        return
            "<li>\n" .
            "    {$scope}\n" .
            "    {$key}\n" .
            "    {$content}\n" .
            "</li>" . "\n";
    }
}
// class Html

<?php

namespace Remix\Preset;

class MimeType
{
    private static $types = [
        'text' => [
            'type' => 'text/plain',
            'console' => false,
        ],
        'html' => [
            'type' => 'text/html',
            'console' => true,
        ],
        'json' => [
            'type' => 'application/json',
            'console' => false,
        ],
        'xml' => [
            'type' => 'application/xml',
            'console' => false,
        ],
        'stream' => [
            'type' => 'application/octet-stream',
            'console' => false,
        ],
    ];

    public static function get(string $type): array
    {
        return static::$types[$type] ?? static::$types['html'];
    }
    // function get()
}
// class MimeType

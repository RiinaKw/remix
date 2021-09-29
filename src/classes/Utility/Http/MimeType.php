<?php

namespace Utility\Http;

/**
 * Mime type
 *
 * @package  Utility\Http
 */
final class MimeType
{
    private const TYPES = [
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
        return self::TYPES[$type] ?? self::TYPES['html'];
    }
    // function get()
}
// class MimeType

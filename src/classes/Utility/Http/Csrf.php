<?php

namespace Utility\Http;

use Utility\Singleton;

/**
 * Class for CSRF measures
 *
 * @package  Utility\Http
 */
class Csrf
{
    use Singleton;

    private static $session = null;
    private static $post = null;

    /**
     * @todo  These keys must be set from the presets
     */
    private static $post_key = 'csrf_token';
    private static $session_token_key = 'sess_csrf_token';
    private static $session_error_key = 'sess_csrf_error';
    private static $token_salt = 'my token salt';

    public static function init()
    {
        static::$session = Session::hash();
        static::$post = PostHash::factory();
    }

    protected static function crypt(string $token)
    {
        return crypt($token, static::$token_salt);
    }

    public static function check(): bool
    {
        $input_token = static::$post->get(static::$post_key, '');

        $session_token = static::$session->get(static::$session_token_key, '');
        $session_crypted = static::crypt($session_token);

        static::$session->delete(static::$session_token_key);

        if (! hash_equals($input_token, $session_crypted)) {
            static::$session->set(static::$session_error_key, 'Illegal screen transition');
            return false;
        }
        return true;
    }

    public function token(): string
    {
        $token = bin2hex(random_bytes(32));
        static::$session->set(static::$session_token_key, $token);
        return static::crypt($token);
    }

    public function error(): string
    {
        $error = static::$session->get(static::$session_error_key, '');
        static::$session->delete(static::$session_error_key);
        return $error;
    }
}

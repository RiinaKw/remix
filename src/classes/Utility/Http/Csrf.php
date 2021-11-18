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

    private static $post = null;

    /**
     * @todo  These keys must be set from the presets
     */
    // private static $post_key = 'csrf_token';
    // private static $session_token_key = 'sess_csrf_token';
    // private static $session_error_key = 'sess_csrf_error';
    private static $token_salt = 'my token salt';

    private static $session_token = null;
    private static $session_error = null;

    public static function init()
    {
        static::$post = PostHash::factory()->item('csrf_token');

        static::$session_token = Session::hash()->item('sess_csrf_token');
        static::$session_error = Session::hash()->item('sess_csrf_error');
    }

    protected static function crypt(string $token)
    {
        return crypt($token, static::$token_salt);
    }

    public static function check(): bool
    {
        $input_token = static::$post->get('');

        $session_token = static::$session_token->get('');
        $session_crypted = static::crypt($session_token);

        static::$session_token->delete();

        if (! hash_equals($input_token, $session_crypted)) {
            static::$session_error->set('Illegal screen transition');
            return false;
        }
        return true;
    }

    public function token(): string
    {
        $token = bin2hex(random_bytes(32));
        static::$session_token->set($token);
        return static::crypt($token);
    }

    public function error(): string
    {
        $error = static::$session_error->get('');
        static::$session_error->delete();
        return $error;
    }
}

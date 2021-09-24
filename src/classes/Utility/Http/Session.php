<?php

namespace Utility\Http;

use Utility\Singleton;

/**
 * HTTP Session
 *
 * @package  Utility\Http
 * @see \Utility\Http\SessionHash
 */
class Session
{
    use Singleton;

    /**
     * @property self $instance
     */

    /**
     * Session Hash
     * @var \Utility\Http\SessionHash
     */
    private $session = null;

    private function __construct()
    {
        static::start();
        $this->session = SessionHash::factory();
        $this->session->ref(static::globals());
    }

    /**
     * Start session
     */
    public static function start(): void
    {
        if (static::globals() === null) {
            session_start();
        }
    }

    /**
     * Get $_SESSION
     * @return null|array<string, mixed>  ref of $_SESSION
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    private static function &globals(): ?array
    {
        return $_SESSION;
    }

    /**
     * Session hash
     * @return SessionHash  Hash of $_SESSION
     */
    public static function hash(): SessionHash
    {
        return static::factory()->session;
    }
}
// class Session

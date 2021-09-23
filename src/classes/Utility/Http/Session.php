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
        $this->session = static::hash();
    }

    /**
     * Start session
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function start(): void
    {
        if (! isset($_SESSION)) {
            session_start();
        }
    }

    /**
     * Get $_SESSION
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function &globals(): array
    {
        return $_SESSION;
    }

    /**
     * Session hash
     * @return SessionHash  Hash of $_SESSION
     */
    public static function hash(): SessionHash
    {
        return SessionHash::factory();
    }
}
// class Session

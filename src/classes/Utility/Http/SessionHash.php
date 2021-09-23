<?php

namespace Utility\Http;

use Utility\Hash\ReadOnlyHash;
use Utility\Hash\Editable;
use Utility\Singleton;

/**
 * HTTP SESSION Hash
 *
 * @package  Utility\Http
 */
class SessionHash extends ReadOnlyHash
{
    use Editable;
    use Singleton;

    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected function __construct()
    {
        parent::__construct();

        if (! isset($_SESSION)) {
            session_start();
        }
        $this->ref($_SESSION);
    }
}
// class GetHash

<?php

namespace Utility\Http;

use Utility\Hash\ReadOnlyHash;
use Utility\Singleton;

/**
 * HTTP GET Hash
 *
 * @package  Utility\Http
 */
class GetHash extends ReadOnlyHash
{
    use Singleton;

    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected function __construct()
    {
        parent::__construct();
        $this->ref($_GET);
    }
}
// class GetHash

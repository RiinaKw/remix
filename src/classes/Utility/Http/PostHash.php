<?php

namespace Utility\Http;

use Utility\Hash\ReadOnlyHash;
use Utility\Singleton;

/**
 * HTTP POST Hash
 *
 * @package  Utility\Http
 */
class PostHash extends ReadOnlyHash
{
    use Singleton;

    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected function __construct()
    {
        parent::__construct();
        $this->ref($_POST);
    }
}
// class PostHash

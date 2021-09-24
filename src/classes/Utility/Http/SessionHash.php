<?php

namespace Utility\Http;

use Utility\Hash\ReadOnlyHash;
use Utility\Hash\Editable;
use Utility\Singleton;
use Utility\Http\Session;

/**
 * HTTP SESSION Hash
 *
 * @package  Utility\Http
 * @see \Utility\Http\Session
 */
class SessionHash extends ReadOnlyHash
{
    use Editable;
    use Singleton;

    protected function __construct()
    {
        parent::__construct();
    }
}
// class GetHash

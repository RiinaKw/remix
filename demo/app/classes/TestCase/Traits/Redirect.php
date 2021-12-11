<?php

namespace RemixDemo\TestCase\Traits;

use Remix\Studio;
use Remix\Lyric;

/**
 * PHPUnit TestCase trait for HTTP redirection.
 * @package  TestCase\Demo\Traits
 */
trait Redirect
{
    /**
     * @property Studio $remixStudio
     */

    protected function assertRedirectUri(string $uri): void
    {
        $this->assertSame($uri, $this->remixStudio->getRedirectUri());
    }

    protected function assertRedirectPath(string $path): void
    {
        $uri = Lyric::getInstance()->sing($path);
        $this->assertRedirectUri($uri);
    }

    protected function assertRedirectName(string $name): void
    {
        $uri = Lyric::getInstance()->named($name);
        $this->assertRedirectUri($uri);
    }
}

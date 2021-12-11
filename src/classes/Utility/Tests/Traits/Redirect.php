<?php

namespace Utility\Tests\Traits;

use Remix\Studio;
use Remix\Lyric;

trait Redirect
{
    /**
     * @property Studio $studio
     */

    protected function assertRedirectUri(string $uri): void
    {
        $this->assertSame($uri, $this->studio->getRedirectUri());
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

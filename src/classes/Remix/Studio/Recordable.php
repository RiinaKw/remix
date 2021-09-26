<?php

namespace Remix\Studio;

/**
 * Recordable base trait of Remix
 *
 * @package  Remix\Base
 * @todo Write the details.
 */
trait Recordable
{
    public function record(): string
    {
        return '';
    }

    public function __set(string $name, $value): void
    {
        $this->setEscaped($name, $value);
    }

    public function setEscaped(string $name, $value): void
    {
        $this->props->push('escaped_params', $value, $name);
    }

    public function setHtml(string $name, $value): void
    {
        $this->props->push('html_params', $value, $name);
    }
}
// trait Recordable

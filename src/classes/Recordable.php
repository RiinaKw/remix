<?php

namespace Remix;

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
        $this->property->push('escaped_params', $value, $name);
    }

    public function setHtml(string $name, $value): void
    {
        $this->property->push('html_params', $value, $name);
    }
}
// trait Recordable

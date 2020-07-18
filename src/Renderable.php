<?php

namespace Remix;

trait Renderable
{
    protected $status;

    public function render()
    {
        http_response_code($this->status);
    } // function render()
} // trait Renderable

<?php

namespace Remix;

trait Recordable
{
    protected $status;

    public function record()
    {
        http_response_code($this->status);
    } // function record()
} // trait Recordable

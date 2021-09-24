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
        //if (class_uses($value)[])
        //var_dump(get_class($value));
        //var_dump(is_subclass_of($value, __CLASS__));
        if (is_object($value)) {
            $traits = class_uses($value);
            if (isset($traits[__TRAIT__])) {
                //var_dump($value->property->keys());
                /*
                var_dump(
                    array_keys($value->property->escaped_params),
                    "======================================<br />\n"
                );
                */
            }
        }

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

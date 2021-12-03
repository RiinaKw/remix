<?php

namespace Utility;

use ReflectionClass;
use ReflectionProperty;
use ReflectionMethod;

/**
 * Utilities of dumping object variables
 *
 * @package  Utility\Dump
 * @todo Write the details.
 */
class DumpObject
{
    private static $instance = null;

    private $configs = [];

    private function __construct(array $configs = [])
    {
        $this->configs = $configs;
    }

    public static function html(object &$object, array $configs = []): string
    {
        static::$instance = new static($configs);

        return static::$instance->object($object);
        // function html()
    }

    private function properties(ReflectionClass $reflection, object $object): string
    {
        $props_html = '';
        $props = $reflection->getProperties();
        foreach ($props as $prop) {
            if ($prop->isStatic()) {
                continue;
            }
            if ($prop->isPrivate()) {
                $scope = 'private';
            } elseif ($prop->isProtected()) {
                $scope = 'protected';
            } else {
                $scope = 'public';
            }

            $prop->setAccessible(true);
            $value = $prop->getValue($object);

            $props_html .= Html::liHtml(
                Html::quote($prop->name),
                Dump::html($value),
                "{$scope} ({$prop->class})"
            );
        }

        $count = count($props);
        if ($count) {
            $details = Html::detailsHtml(Html::typeHtml("properties"), $props_html, true);
            return Html::liHtml("properties({$count})", $details);
        } else {
            return Html::liHtml("properties({$count})");
        }
        // function properties()
    }

    private function staticProperties(ReflectionClass $reflection, object $object): string
    {
        $props_html = '';
        $props = $reflection->getProperties(ReflectionProperty::IS_STATIC);
        foreach ($props as $prop) {
            if ($prop->isPrivate()) {
                $scope = 'private';
            } elseif ($prop->isProtected()) {
                $scope = 'protected';
            } else {
                $scope = 'public';
            }
            $prop->setAccessible(true);
            $value = $prop->getValue($object);

            $props_html .= Html::liHtml(
                Html::quote($prop->name),
                Dump::html($value),
                "{$scope} static ({$prop->class})"
            );
        }

        $count = count($props);
        if ($count) {
            $details = Html::detailsHtml(Html::typeHtml("properties"), $props_html);
            return Html::liHtml("static properties({$count})", $details);
        } else {
            return Html::liHtml("static properties({$count})");
        }
        // function staticProperties()
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    private function constants(ReflectionClass $reflection, object $object): string
    {
        $consts_html = '';
        $consts = $reflection->getConstants();
        foreach ($consts as $name => $value) {
            $consts_html .= Html::liHtml(
                Html::quote($name),
                Dump::html($value),
                "const"
            );
        }

        $count = count($consts);
        if ($count) {
            $details = Html::detailsHtml(Html::typeHtml("constants"), $consts_html);
            return Html::liHtml("constants({$count})", $details);
        } else {
            return Html::liHtml("constants({$count})");
        }
        // function constants()
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    private function method(ReflectionClass $reflection, object $object): string
    {
        $method_html = '';
        $methods = $reflection->getMethods();
        foreach ($methods as $method) {
            if ($method->isStatic()) {
                continue;
            }
            if ($method->isPrivate()) {
                $scope = 'private';
            } elseif ($method->isProtected()) {
                $scope = 'protected';
            } else {
                $scope = 'public';
            }

            if ($this->configs['doccoment']) {
                $content = $method->getDocComment();
            } else {
                $content = 'function';
            }

            $method_html .= Html::liHtml(
                Html::quote($method->name),
                Dump::html($content),
                "{$scope} method ({$method->class})"
            );
        }

        $count = count($methods);
        if ($count) {
            $details = Html::detailsHtml(Html::typeHtml("functions"), $method_html);
            return Html::liHtml("methods({$count})", $details);
        } else {
            return Html::liHtml("methods({$count})");
        }
        // function method()
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    private function staticMethod(ReflectionClass $reflection, object $object): string
    {
        $method_html = '';
        $methods = $reflection->getMethods(ReflectionMethod::IS_STATIC);
        foreach ($methods as $method) {
            if ($method->isPrivate()) {
                $scope = 'private';
            } elseif ($method->isProtected()) {
                $scope = 'protected';
            } else {
                $scope = 'public';
            }

            if ($this->configs['doccoment']) {
                $content = $method->getDocComment();
            } else {
                $content = 'function';
            }

            $method_html .= Html::liHtml(
                Html::quote($method->name),
                Dump::html($content),
                "{$scope} static method ({$method->class})"
            );
        }

        $count = count($methods);
        if ($count) {
            $details = Html::detailsHtml(Html::typeHtml("functions"), $method_html);
            return Html::liHtml("static methods({$count})", $details);
        } else {
            return Html::liHtml("static methods({$count})");
        }
        // function staticMethod()
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function object(object &$object): string
    {
        $reflection = new ReflectionClass($object);

        $html = '';

        /******** properties ********/
        $html .= $this->properties($reflection, $object);

        /******** static properties ********/
        $html .= $this->staticProperties($reflection, $object);

        /******** constants ********/
        $html .= $this->constants($reflection, $object);

        /******** methods ********/
        $html .= $this->method($reflection, $object);

        /******** static methods ********/
        $html .= $this->staticMethod($reflection, $object);

        /******** class information ********/
        $parent_class = $reflection->getParentClass();
        $is_final = $reflection->isFinal();
        $class = $reflection->name;

        if ($this->configs['doccoment']) {
            $doccoment = $reflection->getDocComment();
            $comment = Html::liHtml(
                "doccomment",
                Dump::html($doccoment),
            );
            $html = $comment . $html;
        }

        if ($is_final) {
            $class = 'final ' . $class;
        }
        if ($parent_class) {
            $class .= ' extends ' . $parent_class->getName();
        }
        $html_title = "object({$class})";

        return Html::detailsHtml(
            Html::typeHtml($html_title),
            $html
        );
        // function object()
    }
    // class VarDumpToHTML
}

<?php

namespace Remix\Effector;

use Remix\Effector;
use Remix\DJ;
use Remix\RemixException;

class Livehouse extends Effector
{
    private static function find(): array
    {
        $livehouse_dir = \Remix\App::getInstance()->daw->appDir('Livehouse');
        $arr = [];

        foreach (glob($livehouse_dir . '/*.php') as $file) {
            if (is_file($file)) {
                $name = basename($file);
                preg_match('/^(\d{8}_\d{6})_(.*?)\.php$/', $name, $matches);
                $time = $matches[1] ?? null;
                $class = $matches[2] ?? null;
                if (! $class) {
                    $message = sprintf('Unexpected file in Livehouse, given "%s"', $name);
                    throw new RemixException($message);
                }

                require($file);
                $class_ns = '\\App\\Livehouse\\' . $class;
                if (! class_exists($class_ns)) {
                    $message = sprintf('The file "%s" doees not contain class "%s"', $name, $class_ns);
                    throw new RemixException($message);
                }

                $livehouse = new $class_ns();

                $arr[$time] = $livehouse;
            }
        }

        return $arr;
    }

    public function open()
    {
        Effector::line('Remix Livehouse open');

        try {
            DJ::back2back()->start();

            $arr = static::find();
            foreach ($arr as &$livehouse) {
                $livehouse->open();
                $livehouse = null;
            }

            DJ::back2back()->success();
        } catch (\Exception $e) {
            DJ::back2back()->fail();
            throw $e;
        }
    }

    public function close()
    {
        Effector::line('Remix Livehouse close');

        try {
            DJ::back2back()->start();

            $arr = static::find();
            $arr = array_reverse($arr);
            foreach ($arr as &$livehouse) {
                $livehouse->close();
                $livehouse = null;
            }

            DJ::back2back()->success();
        } catch (\Exception $e) {
            DJ::back2back()->fail();
            throw $e;
        }
    }
}
// class Livehouse

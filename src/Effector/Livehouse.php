<?php

namespace Remix\Effector;

use Remix\Effector;
use Remix\DJ;
use Remix\RemixException;

class Livehouse extends Effector
{
    private static $table = null;
    private static $vinyl_class = \Remix\Vinyl\Livehouse::class;

    private static function setup(): void
    {
        $table = static::$vinyl_class::table();
        if (! $table->exists()) {
            $table->create([
                'livehouse VARCHAR(255)',
            ]);
        }
        static::$table = $table;
    }

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

                $livehouse = $class_ns::factory(basename($name, '.php'));
                //var_dump($livehouse->asVinyl());
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
            static::setup();

            $arr = static::find();
            foreach ($arr as &$livehouse) {
                //var_dump($livehouse);
                $vinyl = static::$vinyl_class::find($livehouse->name);
                if (! $vinyl) {
                    $livehouse->open();
                    $sql = sprintf(
                        'INSERT INTO `%s` (`%s`) VALUES(:%s);',
                        static::$vinyl_class::$table,
                        static::$vinyl_class::$pk,
                        static::$vinyl_class::$pk
                    );
                    DJ::play($sql, [':' . static::$vinyl_class::$pk => $livehouse->name]);
                    $livehouse = null;
                }
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
            static::setup();

            $arr = static::find();
            $arr = array_reverse($arr);
            foreach ($arr as &$livehouse) {
                $vinyl = static::$vinyl_class::find($livehouse->name);
                if ($vinyl) {
                    $livehouse->close();
                    $sql = sprintf(
                        'DELETE FROM `%s` WHERE %s = :%s;',
                        static::$vinyl_class::$table,
                        static::$vinyl_class::$pk,
                        static::$vinyl_class::$pk
                    );
                    DJ::play($sql, [':' . static::$vinyl_class::$pk => $livehouse->name]);
                    $livehouse = null;
                }
            }

            DJ::back2back()->success();
        } catch (\Exception $e) {
            DJ::back2back()->fail();
            throw $e;
        }
    }
}
// class Livehouse

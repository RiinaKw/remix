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
    // function setup()

    private static function find(): array
    {
        $livehouse_dir = \Remix\App::getInstance()->daw->appDir('Livehouse');
        $arr = [];

        foreach (glob($livehouse_dir . '/*.php') as $file) {
            if (is_file($file)) {
                $name = basename($file);
                preg_match('/^(\d{8}_\d{6}_(.*?))\.php$/', $name, $matches);
                $livehouse_name = $matches[1] ?? null;
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
                $arr[$livehouse_name] = $livehouse;
            }
        }

        return $arr;
    }
    // function find()

    public function open()
    {
        Effector::line('Remix Livehouse open');

        try {
            DJ::back2back()->start();
            static::setup();
            $arr = static::find();
            foreach ($arr as &$livehouse) {
                static::stepOpen($livehouse);
                $livehouse = null;
            }

            DJ::back2back()->success();
        } catch (\Exception $e) {
            DJ::back2back()->fail();
            throw $e;
        }
    }
    // function open()

    private static function stepOpen(\Remix\DJ\Livehouse $livehouse)
    {
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
    // function stepOpen()

    public function close()
    {
        Effector::line('Remix Livehouse close');

        try {
            DJ::back2back()->start();
            static::setup();
            $arr = static::find();

            $sql = sprintf(
                'SELECT * FROM `%s` ORDER BY %s DESC',
                static::$vinyl_class::$table,
                static::$vinyl_class::$pk
            );
            $result = DJ::first($sql, []);

            if ($result) {
                $last = $result['livehouse'];
                static::stepClose($arr[$last]);
            }
            $arr = null;

            DJ::back2back()->success();
        } catch (\Exception $e) {
            DJ::back2back()->fail();
            throw $e;
        }
    }
    // function close()

    private static function stepClose(\Remix\DJ\Livehouse $livehouse)
    {
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
        }
    }
    // function stepClose()
}
// class Livehouse

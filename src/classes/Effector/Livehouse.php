<?php

namespace Remix\Effector;

use Remix\Effector;
use Remix\DJ;
use Remix\RemixException;

final class Livehouse extends Effector
{
    protected const TITLE = 'Remix migration manager.';
    protected static $commands = [
        'open' => 'oepn livehouse',
        'close' => 'close livehouse',
    ];

    private static $table = null;
    private static $vinyl_class = \Remix\Vinyl\Livehouse::class;

    private static function setup(): void
    {
        $table = self::$vinyl_class::table();
        if (! $table->exists()) {
            $table->create([
                'livehouse VARCHAR(255)',
            ]);
        }
        self::$table = $table;
    }
    // function setup()

    private static function find(): array
    {
        $livehouse_dir = \Remix\Audio::getInstance()->daw->appDir('livehouses');
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
                $arr[$livehouse_name] = $livehouse;
            }
        } // foreach (glob())

        return $arr;
    }
    // function find()

    public function open()
    {
        self::line('Remix Livehouse open', 'green');

        try {
            DJ::back2back()->start();
            self::setup();
            $arr = self::find();
            $opened = false;
            foreach ($arr as &$livehouse) {
                $opened = self::stepOpen($livehouse);
                $livehouse = null;
            }
            if (! $opened) {
                self::line('  All livehouses opened', 'red');
            }

            DJ::back2back()->success();
        } catch (\Exception $e) {
            DJ::back2back()->fail();
            throw $e;
        }
    }
    // function open()

    private static function stepOpen(\Remix\DJ\Livehouse $livehouse): bool
    {
        $vinyl = self::$vinyl_class::find($livehouse->name);
        if (! $vinyl) {
            $livehouse->open();
            $sql = sprintf(
                'INSERT INTO `%s` (`%s`) VALUES(:%s);',
                self::$vinyl_class::$table,
                self::$vinyl_class::$pk,
                self::$vinyl_class::$pk
            );
            DJ::play($sql, [':' . self::$vinyl_class::$pk => $livehouse->name]);
            self::line("  + open livehouse '{$livehouse->name}'", 'cyan');
            $livehouse = null;
            return true;
        }
        return false;
    }
    // function stepOpen()

    public function close()
    {
        self::line('Remix Livehouse close', 'green');

        try {
            DJ::back2back()->start();
            self::setup();
            $arr = self::find();

            $sql = sprintf(
                'SELECT * FROM `%s` ORDER BY %s DESC',
                self::$vinyl_class::$table,
                self::$vinyl_class::$pk
            );
            $result = DJ::first($sql, []);

            if ($result) {
                $last = $result['livehouse'];
                self::stepClose($arr[$last]);
            } else {
                self::line('  All livehouses closed', 'red');
            }
            $arr = null;

            DJ::back2back()->success();
        } catch (\Exception $e) {
            DJ::back2back()->fail();
            throw $e;
        }
    }
    // function close()

    private static function stepClose(\Remix\DJ\Livehouse $livehouse): void
    {
        $vinyl = self::$vinyl_class::find($livehouse->name);
        if ($vinyl) {
            $livehouse->close();
            $sql = sprintf(
                'DELETE FROM `%s` WHERE %s = :%s;',
                self::$vinyl_class::$table,
                self::$vinyl_class::$pk,
                self::$vinyl_class::$pk
            );
            DJ::play($sql, [':' . self::$vinyl_class::$pk => $livehouse->name]);
            self::line("  - close livehouse '{$livehouse->name}'", 'cyan');
        }
    }
    // function stepClose()
}
// class Livehouse

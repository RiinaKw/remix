<?php

namespace Remix\Effectors;

use Remix\Audio;
use Remix\Effector;
use Remix\Instruments\DJ;
use Remix\DJ\MC;
use Remix\DJ\Table;
use Remix\DJ\Column;
use Remix\Vinyl\Livehouse as Vinyl;
use Remix\DJ\Livehouse as DJLivehouse;
use Remix\RemixException;

/**
 * Remix Livehouse Effector : migration manager
 *
 * @package  Remix\CLI\Effectors
 * @todo Write the details.
 */
final class Livehouse extends Effector
{
    public const TITLE = 'Remix migration manager.';
    public const COMMANDS = [
        'open' => 'oepn livehouse, "-s" to run step by step',
        'close' => 'close livehouse, "-a" to run all',
    ];

    private static $table = null;
    private static $vinyl_class = Vinyl::class;

    private static $livehouses = [];

    /**
     * Make sure to create a livehouse table
     */
    private static function setup(): void
    {
        $table = self::$vinyl_class::table();
        if (! MC::tableExists($table)) {
            $table->create(function (Table $table) {
                Column::varchar('livehouse', 255)->pk()->append($table);
            });
        }
        self::$table = $table;
        self::$livehouses = static::find();
    }
    // function setup()

    /**
     * Load livehouses of app
     * @return array<string, DJLivehouse>
     */
    private static function find(): array
    {
        $livehouse_dir = Audio::getInstance()->daw->appDir('livehouses');
        $namespace = Audio::getInstance()->preset->get('app.namespace');
        $arr = [];

        foreach (glob($livehouse_dir . '/*.php') as $file) {
            if (is_file($file)) {
                $name = basename($file);
                preg_match('/^(\d{8}_\d{6}_(.*?))\.php$/', $name, $matches);
                $livehouse_name = $matches[1] ?? null;
                $class = $matches[2] ?? null;
                if (! $class) {
                    $message = "Unexpected file in Livehouse, given '{$name}'";
                    throw new RemixException($message);
                }

                // Load livehouse
                $class_ns = '\\' . $namespace . '\\Livehouse\\' . $class;
                if (! class_exists($class_ns)) {
                    require($file);

                    // Does it contain the correct class?
                    if (! class_exists($class_ns)) {
                        $message = "The file '{$name}' doees not contain class '{$class_ns}'";
                        throw new RemixException($message);
                    }
                }

                $livehouse = new $class_ns(basename($name, '.php'));
                $arr[$livehouse_name] = $livehouse;
            }
        } // foreach (glob())

        return $arr;
    }
    // function find()

    /**
     * Get the next livehouse
     * @param  string   $prev    The name of the livehouse that was executed last time
     * @return DJLivehouse|null  Next livehouse, or null when the live house has already finished
     */
    private function next(string $prev): ?DJLivehouse
    {
        $keys = array_keys(static::$livehouses);
        $indexes = array_flip($keys);
        $next_index = $indexes[$prev] + 1;

        if (count($keys) > $next_index) {
            $next_name = $keys[$next_index];
            return static::$livehouses[$next_name];
        }
        return null;
    }

    /**
     * Open a livehouse.
     *
     * @param  DJLivehouse $livehouse  The livehouse to open
     * @return bool                    Success or not
     */
    private static function openOne(DJLivehouse $livehouse): bool
    {
        $vinyl = self::$vinyl_class::find($livehouse->name);
        if (! $vinyl) {
            $sql = sprintf(
                'INSERT INTO `%s` (`%s`) VALUES(:%s);',
                self::$vinyl_class::TABLE,
                self::$vinyl_class::PK,
                self::$vinyl_class::PK
            );
            DJ::play($sql, [':' . self::$vinyl_class::PK => $livehouse->name]);

            $livehouse->open();
            return true;
        }
        return false;
    }
    // function openOne()

    /**
     * Close a livehouse.
     *
     * @param  DJLivehouse $livehouse  The livehouse to close
     * @return bool                    Success or not
     */
    private static function closeOne(DJLivehouse $livehouse): bool
    {
        $vinyl = self::$vinyl_class::find($livehouse->name);
        if ($vinyl) {
            $livehouse->close();
            $sql = sprintf(
                'DELETE FROM `%s` WHERE %s = :%s;',
                self::$vinyl_class::TABLE,
                self::$vinyl_class::PK,
                self::$vinyl_class::PK
            );
            DJ::play($sql, [':' . self::$vinyl_class::PK => $livehouse->name]);
            return true;
        }
        return false;
    }
    // function closeOne()

    /**
     * Open all livehouses.
     */
    private function openAll(): void
    {
        try {
            $opened = false;
            foreach (static::$livehouses as $livehouse) {
                try {
                    $opened = self::openOne($livehouse);
                    if ($opened) {
                        static::opened($livehouse);
                    }
                } catch (\Exception $e) {
                    self::closeOne($livehouse);
                    throw $e;
                }
                $livehouse = null;
            }
            if (! $opened) {
                self::line('  All livehouses opened', 'yellow');
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Open only one of the following livehouses.
     */
    private function openStep(): void
    {
        // Get the last livehouse running at the moment
        $vinyl = self::$vinyl_class::last();

        if ($vinyl) {
            // If the last livehouse exists, determine the next livehouse
            $next_livehouse = $this->next($vinyl->livehouse);
        } else {
            // If the last livehouse does not exist, run the first livehouse
            $next_livehouse = reset(static::$livehouses);
        }

        if (! $next_livehouse) {
            self::line('  All livehouses opened', 'yellow');
            return;
        }

        try {
            // try to open
            $opened = self::openOne($next_livehouse);
            if ($opened) {
                static::opened($next_livehouse);
            }
        } catch (\Exception $e) {
            self::closeOne($next_livehouse);
            throw $e;
        }
    }
    // function openStep()

    /**
     * Close all livehouses.
     */
    private function closeAll(): void
    {
        try {
            $setlist = self::$vinyl_class::reverseOrder();
            if (! count($setlist)) {
                self::line('  All livehouses closed', 'yellow');
                return;
            }

            foreach ($setlist as $item) {
                $livehouse = static::$livehouses[$item->livehouse];
                $closed = self::closeOne($livehouse);
                if ($closed) {
                    static::closed($livehouse);
                }
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }
    // function closeAll()

    /**
     * Close only one of the following livehouses.
     */
    private function closeStep(): void
    {
        try {
            $vinyl = self::$vinyl_class::last();
            if ($vinyl) {
                $livehouse = static::$livehouses[$vinyl->livehouse];
                $closed = self::closeOne($livehouse);
                if ($closed) {
                    static::closed($livehouse);
                }
            } else {
                self::line('  All livehouses closed', 'yellow');
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }
    // function closeStep()

    /**
     * Open command.
     *
     * @param array $args  Arguments of CLI
     */
    public function open(array $args): void
    {
        self::line('  Remix Livehouse open', 'green');
        self::setup();

        if (in_array('-s', $args)) {
            self::line('    now step by step', 'green');
            $this->openStep();
        } else {
            $this->openAll();
        }
    }
    // function open()

    /**
     * Close command.
     *
     * @param array $args  Arguments of CLI
     */
    public function close(array $args): void
    {
        self::line('  Remix Livehouse close', 'green');
        self::setup();

        if (in_array('-a', $args)) {
            $this->closeAll();
        } else {
            self::line('    now step by step', 'green');
            $this->closeStep();
        }
    }

    private static function opened(DJLivehouse $livehouse)
    {
        self::line("  + open livehouse '{$livehouse->name}'", 'cyan');
    }

    private static function closed(DJLivehouse $livehouse)
    {
        self::line("  - close livehouse '{$livehouse->name}'", 'purple');
    }
}
// class Livehouse

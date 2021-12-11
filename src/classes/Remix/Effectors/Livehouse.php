<?php

namespace Remix\Effectors;

// Remix core
use Remix\Audio;
use Remix\Effector;
use Remix\Instruments\DJ;
use Remix\DJ\MC;
use Remix\DJ\Table;
use Remix\DJ\Column;
use Remix\Vinyl\Livehouse as Vinyl;
use Remix\DJ\Livehouse as DJLivehouse;
// Exceptions
use Remix\Exceptions\AppException;

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

    /**
     * Class name of Vinyl
     */
    private static $vinyl_class = Vinyl::class;

    /**
     * List of livehouses
     * @var array<string, DJLivehouse>
     */
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
                    throw new AppException($message);
                }

                // Load livehouse
                $class_ns = '\\' . $namespace . '\\Livehouse\\' . $class;
                if (! class_exists($class_ns)) {
                    require($file);

                    // Does it contain the correct class?
                    if (! class_exists($class_ns)) {
                        $message = "The file '{$name}' doees not contain class '{$class_ns}'";
                        throw new AppException($message);
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
        if ($vinyl) {
            return false;
        }

        $sql = sprintf(
            'INSERT INTO `%s` (`%s`) VALUES(:%s);',
            self::$vinyl_class::TABLE,
            self::$vinyl_class::PK,
            self::$vinyl_class::PK
        );
        DJ::play($sql, [':' . self::$vinyl_class::PK => $livehouse->name]);

        return true;
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
        if (! $vinyl) {
            return false;
        }

        $sql = sprintf(
            'DELETE FROM `%s` WHERE %s = :%s;',
            self::$vinyl_class::TABLE,
            self::$vinyl_class::PK,
            self::$vinyl_class::PK
        );
        DJ::play($sql, [':' . self::$vinyl_class::PK => $livehouse->name]);
        return true;
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
                    if (self::openOne($livehouse)) {
                        static::opened($livehouse);
                    }
                } catch (\Exception $e) {
                    self::closeOne($livehouse);
                    throw $e;
                }
            }
            if (! $opened) {
                static::allOpened();
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
            static::allOpened();
            return;
        }

        try {
            // try to open
            if (self::openOne($next_livehouse)) {
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
                static::allClosed();
                return;
            }

            foreach ($setlist as $item) {
                $livehouse = static::$livehouses[$item->livehouse];
                if (self::closeOne($livehouse)) {
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
            if (! $vinyl) {
                static::allClosed();
                return;
            }

            $livehouse = static::$livehouses[$vinyl->livehouse];
            if (self::closeOne($livehouse)) {
                static::closed($livehouse);
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

    private static function allOpened(): void
    {
        self::line('  All livehouses opened', 'yellow');
    }

    private static function allClosed(): void
    {
        self::line('  All livehouses closed', 'yellow');
    }
}
// class Livehouse

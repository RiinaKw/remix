<?php

namespace Remix;

use \Remix\Utility\Performance\Memory;
use \Remix\Utility\Performance\Time;

class Delay
{
    private $is_debug = false;
    private $time = null;
    private $log = [];

    public function __construct(bool $is_debug)
    {
        $this->is_debug = $is_debug;

        $this->time = new Time;
        $this->time->start();
    }

    public function log(string $type, string $str, string $flag = '') : void
    {
        $flag = $flag ? sprintf('%s ', $flag) : '';
        $log = [
            'type' => $type,
            'log' => $flag . $str,
        ];
        $this->log[] = $log;
        if ($this->is_debug && App::isCli()) {
            static::stderr($log);
        }
    }

    public function logMemory() : void
    {
        $this->log('MEMORY', Memory::get());
    }

    public function logTime() : void
    {
        $this->log('TIME', (string)$this->time->stop());
    }

    protected static function format(Array $log) : string
    {
        return sprintf("[%s] %s", $log['type'], $log['log']);
    }

    protected static function stderr(Array $log)
    {
        $color = '0;37';
        switch ($log['type']) {
            case 'BODY':
                $color = '1;30';
                break;
            case 'TRACE':
                $color = '1;34';
                break;
            case 'MEMORY':
                $color = '1;33';
                break;
            case 'TIME':
                $color = '0;35';
                break;
            case 'QUERY':
                $color = '0;36';
                break;
        }
        fprintf(STDERR, "\033[%sm %s\033[0m\n", $color, static::format($log));
    }

    public function get() : string
    {
        $result = [];
        foreach ($this->log as $log) {
            $result[] = static::format($log);
        }
        return implode("<br />\n", $result);
    }
}

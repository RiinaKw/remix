<?php

namespace Remix\Utility\Performance;

class Time
{
    const NANOSEC_TO_MILISEC = 1000 * 1000;
    const UNIT = 'msec';

    private $start;
    private $end;

    public function start()
    {
        $this->start = hrtime(true);
    }

    public function stop()
    {
        $this->end = hrtime(true);
    }

    public function msec()
    {
        if ($this->start && $this->end) {
            return ($this->end - $this->start) / self::NANOSEC_TO_MILISEC;
        } else {
            return null;
        }
    }

    public function __toString()
    {
        $msec = $this->msec();
        if ($msec !== null) {
            return sprintf('%f %s', $msec, self::UNIT);
        } else {
            return '';
        }
    }
} // class Time

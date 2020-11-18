<?php

namespace Remix\Utility\Performance;

class Time
{
    const NANOSEC_TO_MILISEC = 1000 * 1000;
    const UNIT = 'msec';

    private $start;
    private $end;

    public function start() : self
    {
        $this->start = hrtime(true);
        return $this;
    }

    public function stop() : self
    {
        $this->end = hrtime(true);
        return $this;
    }

    public function msec() : ?float
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

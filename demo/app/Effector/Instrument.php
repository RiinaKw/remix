<?php

namespace App\Effector;

use Remix\Effector;

class Instrument extends Effector
{
    public function index()
    {
        Effector::line('I am Instrument belonging to Audio, which instruments do you like?');
        \Remix\Monitor::dump(1);
        throw new \Exception('test exception from Effector');
    }

    public function piano()
    {
        // title background is yellow
        Effector::line('I like John Cage\'s '
            . "\033[0;30m" . "\033[43m" . '4\'33"' . "\033[0m" .
            '... is it not piano!?');
    }

    public function guitar()
    {
        // message is green
        Effector::line("\033[0;32m" . 'SMOKE ON THE WATER!!!' . "\033[0m");
    }

    public function acid($arg)
    {
        $inst = [ 'TB-303' ];
        if (in_array('-808', $arg)) {
            $inst[] = 'TR-808';
        }
        if (array_key_exists('add', $arg)) {
            $inst[] = $arg['add'];
        }
        \Remix\Monitor::dump($inst);
        if (count($inst) == 1) {
            Effector::line($inst[0] . ' is AWESOME!!!');
        } else {
            Effector::line(implode(' and ', $inst) . ' are AWESOME!!!');
        }
    }
}
// class Instrument

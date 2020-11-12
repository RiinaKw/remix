<?php

namespace App\Effector;

use \Remix\Effector;

class Instrument extends Effector
{

    public function index($arg)
    {
        Effector::line('I am Instrument belonging to App, which instruments do you like?');
    }

    public function piano($arg)
    {
        // title background is yellow
        Effector::line('I like John Cage\'s '
            . "\033[0;30m" . "\033[43m" . '4\'33"' . "\033[0m" .
            '... is it not piano!?');
    }

    public function guitar($arg)
    {
        // message is green
        Effector::line("\033[0;32m" . 'SMOKE ON THE WATER!!!' . "\033[0m");
    }

    public function acid($arg)
    {
        $add = '';

        $inst = [ 'TB-303' ];
        if (in_array('-808', $arg)) {
            $inst[] = 'TR-808';
        }
        if (array_key_exists('add', $arg)) {
            $inst[] = $arg['add'];
        }
        if (count($inst) == 1) {
            Effector::line($inst[0] . ' is AWESOME!!!');
        } else {
            Effector::line(implode(' and ', $inst) . ' are AWESOME!!!');
        }
    }
} // class Instrument

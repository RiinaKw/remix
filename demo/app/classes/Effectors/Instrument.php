<?php

namespace RemixDemo\Effectors;

use Remix\Effector;

/**
 * Example effector for some instruments.
 *
 * @package  Demo\Instruments
 */
class Instrument extends Effector
{
    public const TITLE = 'Example of Effector.';
    public const COMMANDS = [
        '' => 'test to throw exception',
        'piano' => 'play piano',
        'guitar' => 'play guitar',
        'acid' => 'play acid house, Use it like "instrument:acid -808 --add=TR-909"',
    ];

    public function index($arg)
    {
        static::line('I am Instrument belonging to Audio, which instruments do you like?');

        // monitor test
        static::line('monitor test', 'black', 'yellow');
        \Remix\Monitor::dump(1);

        // exception test
        //throw new \Exception('test exception from Effector');
    }

    public function piano()
    {
        // title background is yellow
        static::line(
            'I like John Cage\'s '
            . Effector::color('4\'33"', 'black', 'yellow')
            . '... is it not piano!?'
        );
    }

    public function guitar()
    {
        // message is green
        static::line('SMOKE ON THE WATER!!!', 'green');
    }

    public function acid($arg)
    {
        $inst = [ static::color('TB-303', 'yellow') ];
        if (in_array('-808', $arg)) {
            $inst[] = static::color('TR-808', 'black', 'green');
        }
        if (array_key_exists('add', $arg)) {
            $inst[] = static::color($arg['add'], 'black', 'yellow');
        }
        if (count($inst) == 1) {
            static::line($inst[0] . ' is AWESOME!!!');
        } else {
            static::line(implode(' and ', $inst) . ' are AWESOME!!!');
        }
    }
}
// class Instrument

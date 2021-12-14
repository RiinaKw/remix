<?php

// Be careful of directory settings
$public_dir = __DIR__;
$app_dir = __DIR__ . '/../app';
$project_dir = dirname(dirname(__DIR__));

require_once($project_dir . '/vendor/autoload.php');

use Remix\Audio;

Audio::debug();
echo Audio::getInstance()
    ->daw
    ->initialize($app_dir)
    ->playWeb()
    ->finalize();

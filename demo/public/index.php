<?php

// Be careful of directory settings
$public_dir = __DIR__;
$app_dir = __DIR__ . '/../app';
$project_dir = dirname(dirname(__DIR__));

require_once($project_dir . '/vendor/autoload.php');

echo \Remix\Audio::getInstance(true)
    ->daw
    ->initialize($app_dir)
    ->playWeb();

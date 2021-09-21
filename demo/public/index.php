<?php

// Be careful of directory settings
$public_dir = __DIR__;
$app_dir = __DIR__ . '/../app';
$project_dir = dirname(dirname(__DIR__));

require_once($project_dir . '/vendor/autoload.php');

$daw = \Remix\Audio::getInstance(true)->daw;
echo $daw->initialize($app_dir)->playWeb()->finalize();
$daw = null;

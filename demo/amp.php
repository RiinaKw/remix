<?php

$app_dir = __DIR__;
$root_dir = dirname($app_dir);
$autoload_path = $root_dir . '/vendor/autoload.php';

require_once($autoload_path);

$daw = \Remix\Audio::getInstance(true)->daw;
$daw->initialize($app_dir)->playCli($argv);
$daw = null;

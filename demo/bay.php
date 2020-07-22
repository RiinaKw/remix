<?php

$app_dir = __DIR__;
$root_dir = dirname($app_dir);
$autoload_path = $root_dir . '/vendor/autoload.php';

require_once($autoload_path);

$remix = \Remix\App::getInstance();
$remix->initialize($app_dir)->runCli($argv);

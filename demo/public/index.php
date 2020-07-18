<?php

$root_dir = dirname(dirname(__DIR__));
$app_dir = $root_dir . '/demo';
$autoload_path = $root_dir . '/vendor/autoload.php';

require_once($autoload_path);

$remix = \Remix\App::getInstance();
$remix->initialize($app_dir)->runWeb();

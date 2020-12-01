<?php

$public_dir = __DIR__;
$app_dir = dirname($public_dir);
$root_dir = dirname($app_dir);
$autoload_path = $root_dir . '/vendor/autoload.php';

require_once($autoload_path);

$remix = \Remix\App::getInstance(true)->project;
$studio = $remix->initialize($app_dir)->runWeb($public_dir);
echo $studio;
$studio = null;
$remix = null;

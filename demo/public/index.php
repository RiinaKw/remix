<?php

$public_dir = __DIR__;
$app_dir = dirname($public_dir);
$root_dir = dirname($app_dir);
$autoload_path = $root_dir . '/vendor/autoload.php';

require_once($autoload_path);

$daw = \Remix\App::getInstance(true)->daw;
$studio = $daw->initialize($app_dir)->playWeb($public_dir);
echo $studio;
$studio = null;
$daw = null;

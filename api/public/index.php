<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$localenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__), ".env.local");
$localenv->load();

$core = new Handy\Core();

echo $core->handle();
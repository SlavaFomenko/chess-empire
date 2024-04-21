<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$conn = new \Handy\ORM\Connection();
$conn->connect();

$core = new Handy\Core();

echo $core->handle();
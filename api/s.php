<?php
require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$localenv = Dotenv\Dotenv::createImmutable(__DIR__, ".env.local");
$localenv->load();

$server = new \App\Socket\ChessServer($_ENV["SOCKET_IP"], $_ENV["SOCKET_PORT"]);
$server->run();
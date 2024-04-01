<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';

$core = new ChessFramework\Core();

echo $core->handle();
<?php

require_once __DIR__ . '/vendor/autoload.php';

class EchoServer extends \Handy\Socket\SocketServer {

    function __construct($addr, $port) {
        parent::__construct($addr, $port);
    }

    protected function process($user, $message) {
        foreach ($this->users as $u){
            $this->send($u,$message);
        }
    }
}

$server = new EchoServer("172.25.0.3","9999");
$server->run();
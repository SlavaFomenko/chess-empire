<?php

namespace App\Socket;

use App\Entity\User;
use App\Socket\States\ClientState;
use Handy\Socket\SocketClient;
use Handy\Socket\SocketServer;
use Socket;

class ChessClient extends SocketClient
{
    public ?User $user;

    public ?ClientState $state;

    public string $deviceName;

    public function __construct(SocketServer $server, Socket $socket, string $id, string $deviceName = "Unknown Device")
    {
        parent::__construct($server, $socket, $id);
        $this->user = null;
        $this->state = null;
        $this->deviceName = $deviceName;
    }

    public function notifyListeners(string $event, mixed $data, ?SocketClient $client = null): void
    {
        $this->state?->notifyListeners($event, $data, $client);
        parent::notifyListeners($event, $data, $client);
    }

    public function setState(string $state): void
    {
        if(!is_subclass_of($state, ClientState::class)){
            trigger_error($state . " is not inherited from" . ClientState::class . " and cannot be applied");
            return;
        }
        $this->state = new $state($this);
    }
}
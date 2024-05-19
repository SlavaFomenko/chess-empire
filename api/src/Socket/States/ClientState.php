<?php

namespace App\Socket\States;

use App\Socket\ChessClient;

class ClientState
{
    public ChessClient $client;

    public array $events;

    public function __construct(ChessClient $client)
    {
        $this->client = $client;
        $this->events = [];
    }

    public function notifyListeners(string $event, mixed $data, ?ChessClient $client): void
    {
        if (isset($this->events[$event])) {
            $this->events[$event]($data, $client);
        }
    }
}
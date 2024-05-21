<?php

namespace Handy\Socket;

class SocketRoom implements IEventFlow
{

    /**
     * @var SocketServer
     */
    public SocketServer $server;
    /**
     * @var string
     */
    public string $id;
    /**
     * @var array
     */
    public array $clients;
    /**
     * @var array
     */
    public array $events;

    /**
     * @param SocketServer $server
     * @param string $id
     */
    public function __construct(SocketServer $server, string $id)
    {
        $this->server = $server;
        $this->id = $id;
        $this->clients = [];
        $this->events = [];
    }

    /**
     * @param SocketClient $client
     * @return void
     */
    public function join(SocketClient $client): void
    {
        if ($client->room !== null) {
            $this->server->getRoomById($client->room)?->kick($client);
        }
        $client->room = $this->id;
        $this->clients[] = $client->id;
    }

    /**
     * @param SocketClient $client
     * @return void
     */
    public function kick(SocketClient $client): void
    {
        $client->room = null;
        $this->clients = array_diff($this->clients, [$client->id]);
    }

    /**
     * @inheritDoc
     */
    public function on(string $event, object $callback): void
    {
        if (!isset($this->events[$event])) {
            $this->events[$event] = [];
        }
        $this->events[$event][] = $callback;
    }

    /**
     * @inheritDoc
     */
    public function removeListener(string $event, object $callback): void
    {
        if (!isset($this->events[$event])) {
            return;
        }
        $this->events[$event] = array_filter($this->events[$event], fn($cb) => $cb !== $callback);
    }

    /**
     * @inheritDoc
     */
    public function removeAllListeners(string $event): void
    {
        unset($this->events[$event]);
    }

    /**
     * @param string $event
     * @param mixed $data
     * @param SocketClient|null $client
     * @return void
     */
    public function notifyListeners(string $event, mixed $data, ?SocketClient $client = null): void
    {
        if (isset($this->events[$event])) {
            foreach ($this->events[$event] as $listener) {
                $listener($data, $client);
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function clearEvents(): void
    {
        $this->events = [];
    }

}
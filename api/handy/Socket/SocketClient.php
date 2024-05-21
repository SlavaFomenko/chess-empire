<?php

namespace Handy\Socket;

use Socket;

class SocketClient implements IEventFlow
{

    /**
     * @var SocketServer
     */
    public SocketServer $server;
    /**
     * @var Socket
     */
    public Socket $socket;
    /**
     * @var string
     */
    public string $id;
    /**
     * @var string|null
     */
    public ?string $room;
    /**
     * @var array
     */
    public array $headers;
    /**
     * @var string|null
     */
    public ?string $handshake;
    /**
     * @var bool
     */
    public bool $handlingPartialPacket;
    /**
     * @var string
     */
    public string $partialBuffer;
    /**
     * @var bool
     */
    public bool $sendingContinuous;
    /**
     * @var string
     */
    public string $partialMessage;
    /**
     * @var bool
     */
    public bool $hasSentClose;
    /**
     * @var mixed
     */
    public mixed $requestedResource;
    /**
     * @var array
     */
    public array $events;

    /**
     * @param SocketServer $server
     * @param Socket $socket
     * @param string $id
     */
    public function __construct(SocketServer $server, Socket $socket, string $id)
    {
        $this->server = $server;
        $this->socket = $socket;
        $this->id = $id;
        $this->room = null;
        $this->headers = [];
        $this->handshake = null;
        $this->handlingPartialPacket = false;
        $this->partialBuffer = "";
        $this->sendingContinuous = false;
        $this->partialMessage = "";
        $this->hasSentClose = false;
        $this->events = [];
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

    /**
     * @param string $event
     * @param mixed $data
     * @return void
     */
    public function emit(string $event, mixed $data): void
    {
        $this->server->send($this, json_encode([
            "event" => $event,
            "data"  => $data
        ]));
    }

}
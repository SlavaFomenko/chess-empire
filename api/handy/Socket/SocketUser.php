<?php

namespace Handy\Socket;

use Socket;

class SocketUser
{
    public Socket $socket;
    public string $id;
    public array $headers;
    public ?string $handshake;
    public bool $handlingPartialPacket;
    public string $partialBuffer;
    public bool $sendingContinuous;
    public string $partialMessage;
    public bool $hasSentClose;
    public mixed $requestedResource;

    /**
     * @param Socket $socket
     * @param string $id
     */
    public function __construct(Socket $socket, string $id)
    {
        $this->socket = $socket;
        $this->id = $id;
        $this->headers = [];
        $this->handshake = null;
        $this->handlingPartialPacket = false;
        $this->partialBuffer = "";
        $this->sendingContinuous = false;
        $this->partialMessage = "";
        $this->hasSentClose = false;
    }

}
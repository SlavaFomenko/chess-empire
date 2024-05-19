<?php

namespace Handy\Socket;

interface IEventFlow
{

    /**
     * @param string $event
     * @param object $callback
     * @return void
     */
    public function on(string $event, object $callback): void;

    /**
     * @param string $event
     * @param object $callback
     * @return void
     */
    public function removeListener(string $event, object $callback): void;

    /**
     * @param string $event
     * @return void
     */
    public function removeAllListeners(string $event): void;

    /**
     * @param string $event
     * @param mixed $data
     * @param SocketClient|null $client
     * @return void
     */
    public function notifyListeners(string $event, mixed $data, ?SocketClient $client): void;

    /**
     * @return void
     */
    public function clearEvents(): void;
}
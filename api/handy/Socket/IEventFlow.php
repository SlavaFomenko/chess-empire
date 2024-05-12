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
     * @param SocketUser|null $user
     * @return void
     */
    public function notifyListeners(string $event, mixed $data, ?SocketUser $user): void;

    /**
     * @return void
     */
    public function clearEvents(): void;
}
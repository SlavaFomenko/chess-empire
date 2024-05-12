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
    public array $users;
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
        $this->users = [];
        $this->events = [];
    }

    /**
     * @param SocketUser $user
     * @return void
     */
    public function join(SocketUser $user): void
    {
        if ($user->room !== null) {
            $this->server->getRoomById($user->room)?->kick($user);
        }
        $user->room = $this->id;
        $this->users[] = $user->id;
    }

    /**
     * @param SocketUser $user
     * @return void
     */
    public function kick(SocketUser $user): void
    {
        $user->room = null;
        $this->users = array_diff($this->users, [$user->id]);
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
     * @param SocketUser|null $user
     * @return void
     */
    public function notifyListeners(string $event, mixed $data, ?SocketUser $user = null): void
    {
        if (isset($this->events[$event])) {
            foreach ($this->events[$event] as $listener) {
                $listener($data, $user);
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
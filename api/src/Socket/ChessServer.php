<?php

namespace App\Socket;

use App\Socket\States\DefaultState;
use App\Socket\States\SearchingGameState;
use App\Socket\States\UnauthorizedState;
use Handy\Context;
use Handy\ORM\Connection;
use Handy\ORM\EntityManager;
use Handy\Socket\SocketClient;
use Handy\Socket\SocketServer;

class ChessServer extends SocketServer
{

    public array $users;
    public array $randomSearch;
    public EntityManager $em;

    public function __construct(string $ip, int $port, int $maxBufferSize = 2048, string $userClass = ChessClient::class)
    {
        parent::__construct($ip, $port, $maxBufferSize, $userClass);
        Context::$connection = new Connection();
        Context::$connection->connect();
        Context::$entityManager = new EntityManager();
        $this->em = Context::$entityManager;
        $this->users = [];
        $this->randomSearch = [];
        $this->initListeners();
    }

    public function tick(): void
    {
        /** @var GameRoom $room */
        foreach (array_filter($this->rooms, fn($r) => is_a($r, GameRoom::class)) as $room) {
            $room->refreshTimers();
        }
    }

    public function connected(SocketClient $client): void
    {
        /** @var ChessClient $client */
        $client->setState(UnauthorizedState::class);
    }

    public function closed(SocketClient $client): void
    {
        $this->randomSearch = array_filter($this->randomSearch, fn($c) => $c["client"] !== $client->id);

        /** @var ChessClient $client */
        $id = $client->user?->getId();

        if ($id !== null && !isset($this->users[$id])) {
            $this->users[$id] = array_diff($this->users[$id], [$client->id]);
        }
    }

    public function initListeners(): void
    {
        $this->on("play_random", function ($data, ChessClient $client) {
            if (!isset($data["time"], $data["rated"], $data["color"])) {
                trigger_error("Incomplete game settings received");
                return;
            }

            $id = $client->user->getId();

            foreach ($this->users[$id] as $clientId) {
                /** @var ChessClient $c */
                $c = $this->getClientById($clientId);
                if ($c === null) {
                    $this->users[$id] = array_diff($this->users[$id], [$clientId]);
                    continue;
                }
                if (!is_a($c->state, DefaultState::class)) {
                    $client->emit("play_random_err", null);
                    return;
                }
            }

            $availablePlayers = array_filter($this->randomSearch, function ($settings) use ($data) {
                $time = $settings["time"] === $data["time"];
                $rated = $settings["rated"] === $data["rated"];
                $color = $settings["color"] !== $data["color"] || str_contains($settings["color"] . $data["color"], "r");
                return $time && $rated && $color;
            });

            if (empty($availablePlayers)) {
                $client->setState(SearchingGameState::class);
                $this->randomSearch[] = array_merge($data, ["client" => $client->id]);
                return;
            }

            usort($availablePlayers, function ($a, $b) use ($client) {
                $ua = $this->getClientById($a["client"])?->user;
                $ub = $this->getClientById($b["client"])?->user;
                return ($ua->getRating() - $client->user->getRating()) - ($ub->getRating() - $client->user->getRating());
            });

            $secondPlayer = $availablePlayers[0];

            @$this->randomSearch = array_diff($this->randomSearch, [$secondPlayer]);

            $roomId = uniqid('gr');
            $room = new GameRoom($data["rated"], $data["time"] * 60, $this, $roomId);
            $availableColors = array_diff([
                "black",
                "white"
            ], [
                $secondPlayer["color"],
                $data["color"]
            ]);
            shuffle($availableColors);
            if ($data["color"] === "r") {
                $data["color"] = $availableColors[0];
                array_shift($availableColors);
            }
            if ($secondPlayer["color"] === "r") {
                $secondPlayer["color"] = $availableColors[0];
            }

            $room->join($client, $data["color"]);
            $room->join($this->getClientById($secondPlayer["client"]), $secondPlayer["color"]);
            $room->startGame();

            $this->rooms[$roomId] = $room;
        });

        $this->on("cancel_random", function ($data, ChessClient $client) {
            $client->setState(DefaultState::class);
            $this->randomSearch = array_filter($this->randomSearch, fn($c) => $c["client"] !== $client->id);
        });

        $this->on("play_friend", function ($data, ChessClient $client) {
            if (!isset($data["time"], $data["rated"], $data["color"], $data["friendId"])) {
                trigger_error("Incomplete game settings received");
                return;
            }

            $id = $client->user->getId();

            foreach ($this->users[$id] as $clientId) {
                /** @var ChessClient $c */
                $c = $this->getClientById($clientId);
                if ($c === null) {
                    $this->users[$id] = array_diff($this->users[$id], [$clientId]);
                    continue;
                }
                if (!is_a($c->state, DefaultState::class)) {
                    $client->emit("play_friend_err", null);
                    return;
                }
            }

            if (!isset($this->users[$data["friendId"]]) || !empty(array_filter($this->users[$data["friendId"]], function ($clientId) {
                    $client = $this->getClientById($clientId);
                    return !is_a($client?->state, DefaultState::class) && $client !== null;
                }))) {
                $client->emit("play_friend_err", "Friend cannot accept the invite right now");
                return;
            }

            $roomId = uniqid('gr');
            $room = new GameRoom($data["rated"], $data["time"] * 60, $this, $roomId);
            $availableColors = array_diff([
                "black",
                "white"
            ], [$data["color"]]);
            shuffle($availableColors);
            if ($data["color"] === "r") {
                $data["color"] = $availableColors[0];
                array_shift($availableColors);
            }

            $room->join($client, $data["color"]);

            array_map(fn($clientId) => $this->getClientById($clientId)?->emit("game_invite", $room->getGameState()), $this->users[$data["friendId"]]);

            $this->rooms[$roomId] = $room;
        });

        $this->on("game_accept", function ($data, ChessClient $client) {
            $room = @$this->rooms[$data];

            if (!$room) {
                return;
            }

            /** @var GameRoom $room */
            $color = $room->getGameState()["black"]["id"] === null ? "black" : "white";

            $room->join($client, $color);
            $room->startGame();
        });

        $this->on("game_reject", function ($data, ChessClient $client) {
            $room = @$this->rooms[$data];

            if (!$room) {
                return;
            }

            /** @var GameRoom $room */
            foreach ($room->clients as $clientId) {
                $client = $this->getClientById($clientId);
                if (!$client) return;
                $client->emit("game_leave", "Opponent didn't accept the invite");
                $room->kick($client);
                $client->setState(DefaultState::class);
            }
        });
    }

}
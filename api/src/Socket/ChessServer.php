<?php

namespace App\Socket;

use App\Entity\User;
use App\Socket\States\DefaultState;
use App\Socket\States\UnauthorizedState;
use Exception;
use Handy\Context;
use Handy\ORM\Connection;
use Handy\ORM\EntityManager;
use Handy\Security\JWTSecurityProvider;
use Handy\Socket\SocketClient;
use Handy\Socket\SocketServer;

class ChessServer extends SocketServer
{

    public array $users = [];
    public array $randomSearch = [];

    public function __construct(string $ip, int $port, int $maxBufferSize = 2048, string $userClass = ChessClient::class)
    {
        parent::__construct($ip, $port, $maxBufferSize, $userClass);
        Context::$connection = new Connection();
        Context::$connection->connect();
        Context::$entityManager = new EntityManager();
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
        $this->randomSearch = array_filter($this->randomSearch, fn($c)=>$c["client"] !== $client->id);

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

            $availablePlayers = array_filter($this->randomSearch, function ($settings) use ($data) {
                $time = $settings["time"] === $data["time"];
                $rated = $settings["rated"] === $data["rated"];
                $color = $settings["color"] !== $data["color"] || str_contains($settings["color"] . $data["color"], "r");
                return $time && $rated && $color;
            });

            if (empty($availablePlayers)) {
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
            $availableColors = array_diff(["black","white"], [$secondPlayer["color"],$data["color"]]);
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
            $this->randomSearch = array_filter($this->randomSearch, fn($c)=>$c["client"] !== $client->id);
        });
    }

}
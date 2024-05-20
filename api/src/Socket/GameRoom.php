<?php

namespace App\Socket;

use Handy\Socket\SocketClient;
use Handy\Socket\SocketRoom;
use Handy\Socket\SocketServer;

require_once "gameLib.php";

class GameRoom extends SocketRoom
{

    public ?int $startedAt;

    public ?int $lastTimeUpdate;

    public bool $rated;

    public int $time;

    public string $currentTurn;

    public string $winner;

    public array $history;

    public array $players;

    public array $hasMoved;

    public function __construct(bool $rated, int $time, SocketServer $server, string $id)
    {
        parent::__construct($server, $id);
        $this->startedAt = null;
        $this->lastTimeUpdate = null;
        $this->rated = $rated;
        $this->time = $time;
        $this->currentTurn = "-";
        $this->winner = "-";
        $this->hasMoved = [
            "whiteKing"      => false,
            "whiteRookLeft"  => false,
            "whiteRookRight" => false,
            "blackKing"      => false,
            "blackRookLeft"  => false,
            "blackRookRight" => false
        ];
        $this->history = [];
        $this->players = [];
    }

    public function startGame()
    {
        if (!isset($this->players["black"], $this->players["white"])) {
            trigger_error("Cannot start the game before both players join it");
            return;
        }
        if ($this->winner !== "-" || $this->currentTurn !== "-") {
            trigger_error("The game has already started");
            return;
        }
        $this->startedAt = time();
        $this->lastTimeUpdate = $this->startedAt;
        $this->currentTurn = "white";
        foreach ($this->players as $p) {
            $p["client"]->emit("game_update", $this->getGameState());
        }

        $this->on("turn", function ($data, ChessClient $client) {
            $cords = turnToCords($data);
            $apply = applyTurns($this->history);

            $possibleMoves = getPossibleMoves($cords['fromRow'],$cords['fromCol'],$apply["board"],$this->hasMoved);

            var_dump($possibleMoves);

            if (!validateTurn($cords, $apply["board"], $this->currentTurn) && !str_contains($data, "00") && strlen($data) !== 5) {
                return;
            }
            $this->hasMoved = $apply["hasMoved"];
            $this->history[] = $cords;
            $this->currentTurn = $this->currentTurn === "white" ? "black" : "white";
            foreach ($this->players as $p) {
                $p["client"]->emit("game_update", $this->getGameState());
            }
        });
    }



    public function join(SocketClient $client, $color = "-"): void
    {
        /** @var ChessClient $client */
        parent::join($client);
        if (in_array($color, [
            "black",
            "white"
        ])) {
            if (isset($this->players[$color])) {
                trigger_error("Player " . $color . " has already joined");
                $this->kick($client);
                return;
            }

            $this->players[$color] = [
                "client" => $client,
                "time"   => $this->time
            ];
        }
        $client->emit("game_join", $this->getGameState());
    }

    public function refreshTimers(): void
    {
        if ($this->currentTurn === "-") {
            return;
        }

        $now = time();
        $this->players[$this->currentTurn]["time"] -= $now - $this->lastTimeUpdate;
        $this->lastTimeUpdate = $now;
    }

    public function getGameState(): array
    {
        @$b = $this->players["black"];
        @$w = $this->players["white"];
        return [
            "id"       => $this->id,
            "turn"     => $this->currentTurn,
            "history"  => implode(" ", array_map(fn($cords) => cordsToTurn($cords), $this->history)),
            "black"    => [
                "id"       => @$b["client"]->user?->getId(),
                "username" => @$b["client"]->user?->getUserName(),
                "time"     => @$b["time"]
            ],
            "white"    => [
                "id"       => @$w["client"]->user?->getId(),
                "username" => @$w["client"]->user?->getUserName(),
                "time"     => @$w["time"]
            ],
            "hasMoved" => $this->hasMoved
        ];
    }

}
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
        $this->history = array_map(fn($t)=>turnToCords($t), [
            "e7e6",
            "a2a4",
            "d8h4",
            "a1a3",
            "h4a4",
            "h2h4",
            "a4c2",
            "a3h3",
            "h7h5",
            "f2f3",
            "c2d2",
            "e1f2",
            "d2b2",
            "d1d6",
            "b2b1",
            "d6h2",
            "b1c1",
            "f2g3"
        ]);
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
            $apply = applyTurns($this->history, hasMoved:  $this->hasMoved);

            $possibleMoves = getPossibleMoves($cords['fromRow'],$cords['fromCol'],$apply["board"],$this->hasMoved);

            if (!validateTurn($cords, $apply["board"], $this->currentTurn) && !str_contains($data, "00") && strlen($data) !== 5) {
                return;
            }
            $newApply = applyTurns([$cords], $apply["board"], hasMoved:  $apply["hasMoved"]);

            $this->hasMoved = $newApply["hasMoved"];
            $this->history[] = $cords;
            $this->currentTurn = $this->currentTurn === "white" ? "black" : "white";

            foreach ($this->players as $p) {
                $p["client"]->emit("game_update", $this->getGameState());
            }

            if(!canPlayerMove($this->currentTurn, $newApply["board"], $newApply["hasMoved"])){
                $reason = isCheck($this->currentTurn, $newApply["board"]) ? "mate" : "tie";
                $opponent = $this->currentTurn === "white" ? "black" : "white";
                $this->endGame($reason === "tie" ? "tie" : $opponent, $reason);
            }
        });

        $this->on("resign", function ($data, ChessClient $client) {
            $color = current(array_keys(array_filter($this->players, fn($p)=>$p["client"] === $client)));
            if(!$color){
                return;
            }
            $this->endGame($color === "white" ? "black" : "white", "resign");
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

    public function kick(SocketClient $client): void
    {
        parent::kick($client); // TODO: Change the autogenerated stub
        if($this->winner !== "-"){
            return;
        }
        foreach ($this->players as $color => $p) {
            if($p["client"] === $client){
                $this->endGame($color === "white" ? "black" : "white", "disconnect");
            }
        }
    }

    public function endGame($winner, $reason): void
    {
        $this->winner = $winner;
        $this->removeAllListeners("turn");
        $w_rating = $winner === "white" ? 10 : -10;
        $w_rating = $winner === "tie" ? 0 : $w_rating;
        foreach ($this->clients as $clientId){
            $client = $this->server->getClientById($clientId);
            $client->emit("game_end", [
                "winner" => $winner,
                "reason" => $reason,
                "w_rating" => $w_rating,
                "b_rating" => -$w_rating
            ]);
            $this->kick($client);
        }
        unset($this->server->rooms[$this->id]);
    }

    public function refreshTimers(): void
    {
        if ($this->currentTurn === "-") {
            return;
        }

        $now = time();
        $this->players[$this->currentTurn]["time"] -= $now - $this->lastTimeUpdate;
        $this->lastTimeUpdate = $now;

        if($this->players[$this->currentTurn]["time"] <= 0){
            $this->endGame($this->currentTurn === "white" ? "black" : "white", "timeout");
        }

        @$b = $this->players["black"];
        @$w = $this->players["white"];

        foreach ($this->players as $p) {
            $p["client"]->emit("game_timer_update", [
                "black"    => @$b["time"],
                "white"    => @$w["time"]
            ]);
        }
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
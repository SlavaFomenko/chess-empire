<?php

namespace App\Socket;

use App\Entity\Game;
use App\Entity\RatingRange;
use App\Entity\User;
use App\Socket\States\DefaultState;
use App\Socket\States\InGameState;
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
        $this->hasMoved = DEFAULT_HAS_MOVED;
        $this->history = [];
        $this->players = [];

        $this->on("transfer_game", function ($data, ChessClient $client) {
            $target = $this->server->getClientById($data);
            if (empty($target)) {
                return;
            }

            if ($client->user->getId() === $this->players["black"]["client"]?->user?->getId()) {
                $color = "black";
            } else if ($client->user->getId() === $this->players["white"]["client"]?->user?->getId()) {
                $color = "white";
            } else {
                return;
            }

            $this->kick($client, true);
            $this->join($target, $color);
        });
    }

    public function startGame(): void
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
            $p["client"]?->emit("game_update", $this->getGameState());
        }

        $this->on("turn", function ($data, ChessClient $client) {
            /** @var array $cords */
            $cords = turnToCords($data);
            $apply = applyTurns($this->history, hasMoved: $this->hasMoved);

            if (!validateMove($apply["board"], $cords["from"], $cords["to"], $this->hasMoved)) {
                return;
            }

            $newApply = applyTurns([$cords], $apply["board"], hasMoved: $apply["hasMoved"]);

            $this->hasMoved = $newApply["hasMoved"];
            $this->history[] = $cords;
            $this->currentTurn = $this->currentTurn === "white" ? "black" : "white";

            foreach ($this->players as $p) {
                $p["client"]?->emit("game_update", $this->getGameState());
            }

            if (!canPlayerMove($this->currentTurn, $newApply["board"], $newApply["hasMoved"])) {
                $reason = isCheck($newApply["board"], $this->currentTurn, $newApply["hasMoved"]) ? "mate" : "tie";
                $opponent = $this->currentTurn === "white" ? "black" : "white";
                $this->endGame($reason === "tie" ? "tie" : $opponent, $reason);
            }
        });

        $this->on("resign", function ($data, ChessClient $client) {
            $color = current(array_keys(array_filter($this->players, fn($p) => $p["client"] === $client)));
            if (!$color) {
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
            if (!isset($this->players[$color])) {
                $this->players[$color] = [
                    "client" => $client,
                    "time"   => $this->time
                ];
            } else {
                if (!empty($this->players[$color]["client"])) {
                    trigger_error("Player " . $color . " has already joined");
                    $this->kick($client);
                    return;
                }
                $this->players[$color]["client"] = $client;
            }
        }
        $client->setState(InGameState::class);
        $client->emit("game_join", $this->getGameState());
    }

    public function kick(SocketClient $client, bool $soft = false): void
    {
        parent::kick($client);
        $client->setState(DefaultState::class);
        if ($this->winner !== "-"){
            return;
        }
        if ($soft === true) {
            $client->emit("game_leave", "The fame was transferred to another device");
            if ($client->user->getId() === $this->players["black"]["client"]?->user?->getId()) {
                $color = "black";
            } else if ($client->user->getId() === $this->players["white"]["client"]?->user?->getId()) {
                $color = "white";
            } else {
                return;
            }
            $this->players[$color]["client"] = null;
            return;
        }
        foreach ($this->players as $color => $p) {
            if ($p["client"] === $client) {
                $this->endGame($color === "white" ? "black" : "white", "disconnect");
            }
        }
    }

    public function mapRanges($value, $fromMin, $fromMax, $toMin, $toMax): float
    {
        $fromRange = $fromMax - $fromMin;
        $toRange = $toMax - $toMin;
        $scale = $toRange / $fromRange;
        return $toMin + ($value - $fromMin) * $scale;
    }

    public function endGame($winner, $reason): void
    {
        $this->winner = $winner;
        $this->removeAllListeners("turn");

        if (@$this->players["black"]["client"]?->user?->getId() === null ||
            @$this->players["white"]["client"]?->user?->getId() === null) {
            unset($this->server->rooms[$this->id]);
            return;
        }

        $rating = [
            "black" => $this->players["black"]["client"]->user->getRating(),
            "white" => $this->players["white"]["client"]->user->getRating()
        ];

        $white_coef = [
            "win"  => 0,
            "loss" => 0
        ];

        if ($winner !== "tie" && $this->rated) {
            $white_coef["win"] = $this->mapRanges($rating["black"] - $rating["white"], -max($rating["black"], $rating["white"]), max($rating["black"], $rating["white"]), 0.5, 1.5);
            $white_coef["loss"] = 2 - $white_coef["win"];
        }

        $ratingRangeRepo = $this->server->em?->getRepository(RatingRange::class);
        /** @var RatingRange $blackRatingRange */
        $blackRatingRange = current($ratingRangeRepo->findBy(["min_rating" => "<= " . $rating["black"]], orderBy: [
            [
                "min_rating",
                "DESC"
            ]
        ]));
        /** @var RatingRange $whiteRatingRange */
        $whiteRatingRange = current($ratingRangeRepo->findBy(["min_rating" => "<= " . $rating["white"]], orderBy: [
            [
                "min_rating",
                "DESC"
            ]
        ]));

        $rating_change = [
            "black" => (int)round($winner === "black" ? $blackRatingRange->getWin() * $white_coef["loss"] : $blackRatingRange->getLoss() * $white_coef["win"], 0),
            "white" => (int)round($winner === "white" ? $whiteRatingRange->getWin() * $white_coef["win"] : $whiteRatingRange->getLoss() * $white_coef["loss"], 0)
        ];

        $gameRecord = new Game();
        $gameRecord->setTime($this->time)
            ->setRated($this->rated)
            ->setWinner($winner[0])
            ->setBlackRating($this->players["black"]["client"]->user->getRating())
            ->setWhiteRating($this->players["white"]["client"]->user->getRating())
            ->setBlackRatingChange($rating_change["black"])
            ->setWhiteRatingChange($rating_change["white"])
            ->setBlackId($this->players["black"]["client"]->user->getId())
            ->setWhiteId($this->players["white"]["client"]->user->getId())
            ->setHistory(implode(" ", array_map(fn($cords) => cordsToTurn($cords), $this->history)))
            ->setPlayedDate($this->startedAt);

        $this->server->em?->persist($gameRecord);

        foreach ($this->players as $color => $player) {
            $player["client"]?->emit("game_end", [
                "winner"              => $winner,
                "reason"              => $reason,
                "white_rating"        => $rating["white"],
                "black_rating"        => $rating["black"],
                "white_rating_change" => $rating_change["white"],
                "black_rating_change" => $rating_change["black"]
            ]);
            $player["client"]->setState(DefaultState::class);
            $this->kick($player["client"]);
            if ($this->rated) {
                $repo = $this->server->em->getRepository(User::class);
                $user = $repo->find($player["client"]->user->getId());
                $player["client"]->user = $user;
                $newRating = $user->getRating() + $rating_change[$color];
                $user->setRating(max($newRating, 0));
                $this->server->em?->persist($user);
            }
        }

        $this->server->em?->flush();

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

        if ($this->players[$this->currentTurn]["time"] <= 0) {
            $this->endGame($this->currentTurn === "white" ? "black" : "white", "timeout");
        }

        @$b = $this->players["black"];
        @$w = $this->players["white"];

        foreach ($this->players as $p) {
            $p["client"]?->emit("game_timer_update", [
                "black" => @$b["time"],
                "white" => @$w["time"]
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
                "id"         => @$b["client"]->user?->getId(),
                "username"   => @$b["client"]->user?->getUserName(),
                "profilePic" => @$b["client"]->user?->getProfilePic(),
                "time"       => @$b["time"]
            ],
            "white"    => [
                "id"         => @$w["client"]->user?->getId(),
                "username"   => @$w["client"]->user?->getUserName(),
                "profilePic" => @$w["client"]->user?->getProfilePic(),
                "time"       => @$w["time"]
            ],
            "hasMoved" => $this->hasMoved
        ];
    }

}
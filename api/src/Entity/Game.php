<?php

namespace App\Entity;

use App\Repository\GameRepository;
use Handy\ORM\Attribute\Column;
use Handy\ORM\Attribute\Entity;
use Handy\ORM\Attribute\Id;
use Handy\ORM\BaseEntity;
use Handy\ORM\ColumnType;
use Handy\Utils\JsonSerializable;

#[Entity(repository: GameRepository::class, table: "game")]
class Game extends BaseEntity implements JsonSerializable
{

    #[Id]
    #[Column(type: ColumnType::INT, column: "id")]
    private ?int $id = null;

    #[Column(type: ColumnType::INT, column: "time")]
    private ?int $time = null;

    #[Column(type: ColumnType::BOOL, column: "rated")]
    private ?bool $rated = false;

    #[Column(type: ColumnType::VARCHAR, column: "winner", length: 1)]
    private ?string $winner = null;

    #[Column(type: ColumnType::INT, column: "black_rating")]
    private ?int $black_rating = null;

    #[Column(type: ColumnType::INT, column: "white_rating")]
    private ?int $white_rating = null;

    #[Column(type: ColumnType::INT, column: "black_id")]
    private ?int $black_id = null;

    #[Column(type: ColumnType::INT, column: "white_id")]
    private ?int $white_id = null;

    #[Column(type: ColumnType::TEXT, column: "history")]
    private ?string $history = null;

    #[Column(type: ColumnType::BIGINT, column: "played_date")]
    private ?string $playedDate = null;

    public function __construct()
    {
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     * @return Game
     */
    public function setId(?int $id): Game
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getTime(): ?int
    {
        return $this->time;
    }

    /**
     * @param int|null $time
     * @return Game
     */
    public function setTime(?int $time): Game
    {
        $this->time = $time;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getRated(): ?bool
    {
        return $this->rated;
    }

    /**
     * @param bool|null $rated
     * @return Game
     */
    public function setRated(?bool $rated): Game
    {
        $this->rated = $rated;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getWinner(): ?string
    {
        return $this->winner;
    }

    /**
     * @param string|null $winner
     * @return Game
     */
    public function setWinner(?string $winner): Game
    {
        $this->winner = $winner;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getBlackRating(): ?int
    {
        return $this->black_rating;
    }

    /**
     * @param int|null $black_rating
     * @return Game
     */
    public function setBlackRating(?int $black_rating): Game
    {
        $this->black_rating = $black_rating;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getWhiteRating(): ?int
    {
        return $this->white_rating;
    }

    /**
     * @param int|null $white_rating
     * @return Game
     */
    public function setWhiteRating(?int $white_rating): Game
    {
        $this->white_rating = $white_rating;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getBlackId(): ?int
    {
        return $this->black_id;
    }

    /**
     * @param int|null $black_id
     * @return Game
     */
    public function setBlackId(?int $black_id): Game
    {
        $this->black_id = $black_id;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getWhiteId(): ?int
    {
        return $this->white_id;
    }

    /**
     * @param int|null $white_id
     * @return Game
     */
    public function setWhiteId(?int $white_id): Game
    {
        $this->white_id = $white_id;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getHistory(): ?string
    {
        return $this->history;
    }

    /**
     * @param string|null $history
     * @return Game
     */
    public function setHistory(?string $history): Game
    {
        $this->history = $history;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPlayedDate(): ?string
    {
        return $this->playedDate;
    }

    /**
     * @param string|null $playedDate
     * @return Game
     */
    public function setPlayedDate(?string $playedDate): Game
    {
        $this->playedDate = $playedDate;
        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            "id" => $this->getId(),
            "time"=>$this->getTime(),
            "rated"=>$this->getRated(),
            "winner"=>$this->getWinner(),
            "black_rating"=>$this->getBlackRating(),
            "white_rating"=>$this->getWhiteRating(),
            "black_id"=>$this->getBlackId(),
            "white_id"=>$this->getWhiteId(),
            "history"=>$this->getHistory(),
            "playedDate"=>$this->getPlayedDate()
        ];
    }

}
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

    #[Column(type: ColumnType::INT, column: "b_rating")]
    private ?int $b_rating = null;

    #[Column(type: ColumnType::INT, column: "w_rating")]
    private ?int $w_rating = null;

    #[Column(type: ColumnType::INT, column: "b_id")]
    private ?int $b_id = null;

    #[Column(type: ColumnType::INT, column: "w_id")]
    private ?int $w_id = null;

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
    public function getBRating(): ?int
    {
        return $this->b_rating;
    }

    /**
     * @param int|null $b_rating
     * @return Game
     */
    public function setBRating(?int $b_rating): Game
    {
        $this->b_rating = $b_rating;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getWRating(): ?int
    {
        return $this->w_rating;
    }

    /**
     * @param int|null $w_rating
     * @return Game
     */
    public function setWRating(?int $w_rating): Game
    {
        $this->w_rating = $w_rating;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getBId(): ?int
    {
        return $this->b_id;
    }

    /**
     * @param int|null $b_id
     * @return Game
     */
    public function setBId(?int $b_id): Game
    {
        $this->b_id = $b_id;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getWId(): ?int
    {
        return $this->w_id;
    }

    /**
     * @param int|null $w_id
     * @return Game
     */
    public function setWId(?int $w_id): Game
    {
        $this->w_id = $w_id;
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
        $data = [
            "id" => $this->getId(),
            "time"=>$this->getTime(),
            "rated"=>$this->getRated(),
            "winner"=>$this->getWinner(),
            "b_rating"=>$this->getBRating(),
            "w_rating"=>$this->getWRating(),
            "b_id"=>$this->getBId(),
            "w_id"=>$this->getWId(),
            "history"=>$this->getHistory(),
            "playedDate"=>$this->getPlayedDate()
        ];
        return $data;
    }

}
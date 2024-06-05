<?php

namespace App\Entity;

use App\Repository\RatingRangeRepository;
use Handy\ORM\ArrayMappable;
use Handy\ORM\Attribute\Column;
use Handy\ORM\Attribute\Entity;
use Handy\ORM\Attribute\Id;
use Handy\ORM\BaseEntity;
use Handy\ORM\ColumnType;
use Handy\Utils\JsonSerializable;

#[Entity(repository: RatingRangeRepository::class, table: "rating_range")]
class RatingRange extends BaseEntity implements JsonSerializable, ArrayMappable
{

    #[Id]
    #[Column(type: ColumnType::INT, column: "id")]
    private ?int $id = null;

    #[Column(type: ColumnType::INT, column: "min_rating")]
    private ?int $minRating = null;

    #[Column(type: ColumnType::INT, column: "win")]
    private ?int $win = null;

    #[Column(type: ColumnType::INT, column: "loss")]
    private ?int $loss = null;

    #[Column(type: ColumnType::VARCHAR, column: "title", length: 255)]
    private ?string $title = null;

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
     * @return self
     */
    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getMinRating(): ?int
    {
        return $this->minRating;
    }

    /**
     * @param int|null $minRating
     * @return self
     */
    public function setMinRating(?int $minRating): self
    {
        $this->minRating = $minRating;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getWin(): ?int
    {
        return $this->win;
    }

    /**
     * @param int|null $win
     * @return self
     */
    public function setWin(?int $win): self
    {
        $this->win = $win;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getLoss(): ?int
    {
        return $this->loss;
    }

    /**
     * @param int|null $loss
     * @return self
     */
    public function setLoss(?int $loss): self
    {
        $this->loss = $loss;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string|null $title
     * @return self
     */
    public function setTitle(?string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            "id"           => $this->getId(),
            "minRating"         => $this->getMinRating(),
            "win"        => $this->getWin(),
            "loss"       => $this->getLoss(),
            "title" => $this->getTitle()
        ];
    }

    public function fromArray(array $arr): self
    {
        isset($arr["minRating"]) && $this->setMinRating($arr["minRating"]);
        isset($arr["win"]) && $this->setWin($arr["win"]);
        isset($arr["loss"]) && $this->setLoss($arr["loss"]);
        isset($arr["title"]) && $this->setTitle($arr["title"]);
        return $this;
    }

}
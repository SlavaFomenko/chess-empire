<?php

namespace App\Entity;

use App\Repository\FriendPairRepository;
use App\Repository\UserRepository;
use Handy\ORM\ArrayMappable;
use Handy\ORM\Attribute\Column;
use Handy\ORM\Attribute\Entity;
use Handy\ORM\Attribute\Id;
use Handy\ORM\BaseEntity;
use Handy\ORM\ColumnType;
use Handy\Utils\JsonSerializable;

#[Entity(repository: FriendPairRepository::class, table: "friend_pair")]
class FriendPair extends BaseEntity implements JsonSerializable
{

    #[Id]
    #[Column(type: ColumnType::INT, column: "id")]
    private ?int $id = null;

    #[Column(type: ColumnType::INT, column: "sender_id")]
    private ?int $senderId = null;

    #[Column(type: ColumnType::INT, column: "receiver_id")]
    private ?int $receiverId = null;

    #[Column(type: ColumnType::BOOL, column: "accepted")]
    private ?int $accepted = null;

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
    public function getSenderId(): ?int
    {
        return $this->senderId;
    }

    /**
     * @param int|null $senderId
     * @return self
     */
    public function setSenderId(?int $senderId): self
    {
        $this->senderId = $senderId;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getReceiverId(): ?int
    {
        return $this->receiverId;
    }

    /**
     * @param int|null $receiverId
     * @return self
     */
    public function setReceiverId(?int $receiverId): self
    {
        $this->receiverId = $receiverId;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getAccepted(): ?int
    {
        return $this->accepted;
    }

    /**
     * @param int|null $accepted
     * @return self
     */
    public function setAccepted(?int $accepted): self
    {
        $this->accepted = $accepted;
        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            "id" => $this->getId(),
            "senderId" => $this->getSenderId(),
            "receiverId" => $this->getReceiverId(),
            "accepted" => $this->getAccepted(),
        ];
    }

}
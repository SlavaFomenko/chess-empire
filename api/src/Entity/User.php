<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Handy\ORM\Attribute\Column;
use Handy\ORM\Attribute\Entity;
use Handy\ORM\Attribute\Id;
use Handy\ORM\BaseEntity;
use Handy\ORM\ColumnType;
use Handy\Utils\JsonSerializable;

#[Entity(repository: UserRepository::class, table: "user")]
class User extends BaseEntity implements JsonSerializable
{

    #[Id]
    #[Column(type: ColumnType::INT, column: "id")]
    private ?int $id = null;

    #[Column(type: ColumnType::VARCHAR, column: "email", length: 255)]
    private ?string $email = null;

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
     * @return User
     */
    public function setId(?int $id): User
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     * @return User
     */
    public function setEmail(?string $email): User
    {
        $this->email = $email;
        return $this;
    }


    public function jsonSerialize(): array
    {
        return [
            "id" => $this->getId(),
            "email" => $this->getId()
        ];
    }

}
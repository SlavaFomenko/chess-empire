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

    public const ROLE_USER = "ROLE_USER";
    public const ROLE_ADMIN = "ROLE_ADMIN";

    #[Id]
    #[Column(type: ColumnType::INT, column: "id")]
    private ?int $id = null;

    #[Column(type: ColumnType::VARCHAR,column: "role",length: 50)]
    private ?string $role = null;

    #[Column(type: ColumnType::VARCHAR, column: "email", length: 255)]
    private ?string $email = null;

    #[Column(type: ColumnType::VARCHAR, column: "password", length: 255)]
    private ?string $hashedPassword = null;

    #[Column(type: ColumnType::VARCHAR, column: "username", length: 100)]
    private ?string $userName = null;

    #[Column(type: ColumnType::VARCHAR, column: "first_name", length: 50)]
    private ?string $firstName = null;

    #[Column(type: ColumnType::VARCHAR, column: "last_name", length: 50)]
    private ?string $lastName = null;

    #[Column(type: ColumnType::INT, column: "rating")]
    private ?int $rating = null;

    public function __construct()
    {
    }

    /**
     * @return string|null
     */
    public function getRole(): ?string
    {
        return $this->role;
    }

    /**
     * @param string|null $role
     */
    public function setRole(?string $role): User
    {
        $this->role = $role;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->hashedPassword;
    }

    /**
     * @param string|null $hashedPassword
     */
    public function setPassword(?string $hashedPassword): User
    {
        $this->hashedPassword = $hashedPassword;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getUserName(): ?string
    {
        return $this->userName;
    }

    /**
     * @param string|null $username
     * @return User
     */
    public function setUserName(?string $username): User
    {
        $this->userName = $username;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @param string|null $firstName
     */
    public function setFirstName(?string $firstName): User
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @param string|null $lastName
     */
    public function setLastName(?string $lastName): User
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getRating(): ?int
    {
        return $this->rating;
    }

    /**
     * @param int|null $rating
     */
    public function setRating(?int $rating): User
    {
        $this->rating = $rating;
        return $this;
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
        $data = [
            "id" => $this->getId(),
            "role"=>$this->getRole(),
            "email" => $this->getEmail(),
            "username"=>$this->getUserName(),
            "firstName"=>$this->getFirstName(),
            "lastName"=>$this->getLastName(),
            "rating"=>$this->getRating()
        ];
        return $data;
    }

}
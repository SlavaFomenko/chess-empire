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

    #[Column(type: ColumnType::VARCHAR, column: "profile_pic", length: 255, nullable: true)]
    private ?string $profilePic = null;

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
     * @return self
     */
    public function setRole(?string $role): self
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
     * @return self
     */
    public function setPassword(?string $hashedPassword): self
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
     * @return self
     */
    public function setUserName(?string $username): self
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
     * @return self
     */
    public function setFirstName(?string $firstName): self
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
     * @return self
     */
    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getProfilePic(): ?string
    {
        return $this->profilePic;
    }

    /**
     * @param string|null $profilePic
     * @return self
     */
    public function setProfilePic(?string $profilePic): self
    {
        $this->profilePic = $profilePic;
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
     * @return self
     */
    public function setRating(?int $rating): self
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
     * @return self
     */
    public function setId(?int $id): self
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
     * @return self
     */
    public function setEmail(?string $email): self
    {
        $this->email = $email;
        return $this;
    }


    public function jsonSerialize(): array
    {
        return [
            "id" => $this->getId(),
            "role"=>$this->getRole(),
            "email" => $this->getEmail(),
            "username"=>$this->getUserName(),
            "firstName"=>$this->getFirstName(),
            "lastName"=>$this->getLastName(),
            "profilePic"=>$this->getProfilePic(),
            "rating"=>$this->getRating()
        ];
    }

}
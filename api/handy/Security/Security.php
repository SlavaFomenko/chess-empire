<?php

namespace Handy\Security;

class Security
{
    public const ROLE_UNAUTHORIZED = "UNAUTHORIZED";

    private ?string $token;
    private string $role;
    private ?object $data;

    public function __construct(?string $token = null, string $role = self::ROLE_UNAUTHORIZED, ?object $data = null)
    {
        $this->token = $token;
        $this->role = $role;
        $this->data = $data;
    }

    /**
     * @return ?object
     */
    public function getData(): ?object
    {
        return $this->data;
    }

    /**
     * @param object $data
     * @return void
     */
    public function setData(object $data): void
    {
        $this->data = $data;
    }


    /**
     * @return string|null
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * @param string $token
     * @return void
     */
    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getRole(): string
    {
        return $this->role;
    }

    /**
     * @param string $role
     * @return void
     */
    public function setRole(string $role): void
    {
        $this->role = $role;
    }

}

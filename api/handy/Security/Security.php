<?php

namespace Handy\Security;

class Security {
    private ?string $token;
    private array $roles;
    private ?object $data;

    public function __construct( $token = null,  $roles=['unauthorized'] , $data = null) {
        $this->token = $token;
        $this->roles = $roles;
        $this->data = $data;
    }
    /**
     * @return object
     */
    public function getData(): object
    {
        return $this->data;
    }

    /**
     * @param object $data
     */
    public function setData(object $data): void
    {
        $this->data = $data;
    }


    /**
     * @return mixed|string|null
     */
    public function getToken() {
        return $this->token;
    }

    /**
     * @param $token
     * @return void
     */
    public function setToken($token) {
        $this->token = $token;
    }

    /**
     * @return mixed|string[]
     */
    public function getRoles() {
        return $this->roles;
    }

    /**
     * @param $roles
     * @return void
     */
    public function setRoles($roles) {
        $this->roles = $roles;
    }
}

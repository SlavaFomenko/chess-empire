<?php

namespace App\Socket\States;

use App\Entity\User;
use App\Socket\ChessClient;
use Exception;
use Handy\Context;
use Handy\Security\JWTSecurityProvider;

class UnauthorizedState extends ClientState
{
    public function __construct(ChessClient $client)
    {
        parent::__construct($client);
        $this->events["auth"] = function ($data, ChessClient $client) {
            $tokenData = null;
            try {
                $tokenData = JWTSecurityProvider::parseToken($data);
            } catch (Exception $e) {
                $client->emit("auth_err", "Invalid token");
                return;
            }

            if (!JWTSecurityProvider::validateToken($data) || !isset($tokenData["id"])) {
                $client->emit("auth_err", "Invalid token");
                return;
            }

            $repo = Context::$entityManager->getRepository(User::class);
            /** @var User $user */
            $user = $repo->find($tokenData["id"]);

            if (!$user) {
                $client->emit("auth_err", "User not found");
                return;
            }

            $id = $user->getId();

            if (!isset($client->server->users[$id])) {
                $client->server->users[$id] = [];
            }
            $client->server->users[$id][] = $client->id;
            $client->user = $user;
            $client->setState(DefaultState::class);
            $client->emit("auth_ok", $client->id);
        };
    }

}
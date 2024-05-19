<?php

namespace App\Socket;

use App\Entity\User;
use App\Socket\States\DefaultState;
use App\Socket\States\UnauthorizedState;
use Exception;
use Handy\Context;
use Handy\ORM\Connection;
use Handy\ORM\EntityManager;
use Handy\Security\JWTSecurityProvider;
use Handy\Socket\SocketServer;
use Handy\Socket\SocketClient;

class ChessServer extends SocketServer
{
    public function __construct(string $ip, int $port, int $maxBufferSize = 2048, string $userClass = ChessClient::class)
    {
        parent::__construct($ip, $port, $maxBufferSize, $userClass);
        Context::$connection = new Connection();
        Context::$connection->connect();
        Context::$entityManager = new EntityManager();
        $this->initListeners();
    }

    public function connected(SocketClient $client): void
    {
        /** @var ChessClient $client */
        $client->setState(UnauthorizedState::class);
    }

    public function initListeners(): void
    {
        $this->on("auth", function ($data, ChessClient $client){
            if(!is_a($client->state, UnauthorizedState::class)){
                $client->emit("auth_err", "Already authorized");
                return;
            }

            $tokenData = null;
            try {
                $tokenData = JWTSecurityProvider::parseToken($data);
            } catch (Exception $e){
                $client->emit("auth_err", "Invalid token");
                return;
            }

            if(!JWTSecurityProvider::validateToken($data) || !isset($tokenData["id"])){
                $client->emit("auth_err", "Invalid token");
                return;
            }

            $repo = Context::$entityManager->getRepository(User::class);
            $user = $repo->find($tokenData["id"]);

            if(!$user){
                $client->emit("auth_err", "User not found");
                return;
            }

            $client->user = $user;
            $client->setState(DefaultState::class);
            $client->emit("auth_ok", $client->id);
        });
    }
}
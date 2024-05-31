<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\User;
use Handy\Context;
use Handy\Controller\BaseController;
use Handy\Http\JsonResponse;
use Handy\Http\Request;
use Handy\Http\Response;
use Handy\Routing\Attribute\Route;

class GameController extends BaseController
{

    #[Route(name: "get_games", path: "/games", methods: [Request::METHOD_GET])]
    public function getForCurrentUser(): Response
    {
        if (current(Context::$security->getRoles()) === "unauthorized") {
            return new JsonResponse(["message" => "Unauthorized"], 401);
        }

        $userRepo = $this->em->getRepository(User::class);
        $user = $userRepo->find(Context::$security->getData()->id);

        if (!$user) {
            return new JsonResponse(["message" => "User not found"], 404);
        }

        [$limit, $offset] = $this->pagination();

        $gameRepo = $this->em->getRepository(Game::class);
        $games = $gameRepo->findBy([
            "b_id" => $user->getId(),
            "w_id" => $user->getId()
        ], true, $limit, $offset, [["played_date", "DESC"]]);

        $result = [];

        $opponents = [];
        /** @var Game $game */
        foreach ($games as $game) {
            $color = $user->getId() === $game->getBId() ? "b" : "w";
            $opponentId = $color === "b" ? $game->getWId() : $game->getBId();
            if(!isset($opponents[$opponentId])){
                $opponent = $userRepo->find($opponentId);
                $opponents[$opponentId] = $opponent;
            }

            $result[] = [
                ...$game->jsonSerialize(),
                $color . "_username" => $user->getUserName(),
                ($color === "b" ? "w" : "b") . "_username" => $opponents[$opponentId]?->getUserName() ?? "UnknownUser"
            ];
        }

        return new JsonResponse($result, 200);
    }

}
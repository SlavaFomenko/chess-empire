<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\User;
use App\Repository\GameRepository;
use Handy\Context;
use Handy\Controller\BaseController;
use Handy\Http\JsonResponse;
use Handy\Http\Request;
use Handy\Http\Response;
use Handy\Routing\Attribute\Route;
use Handy\Security\Security;

class GameController extends BaseController
{

    #[Route(name: "get_games", path: "/games", methods: [Request::METHOD_GET])]
    public function getForCurrentUser(): Response
    {
        if (Context::$security->getRole() === Security::ROLE_UNAUTHORIZED) {
            return new JsonResponse(["message" => "Unauthorized"], 401);
        }

        $userRepo = $this->em->getRepository(User::class);
        $user = $userRepo->find(Context::$security->getData()->id);

        if (!$user) {
            return new JsonResponse(["message" => "User not found"], 404);
        }

        [$limit, $offset] = $this->pagination();

        /** @var GameRepository $gameRepo */
        $gameRepo = $this->em->getRepository(Game::class);

        if(Context::$security->getRole() === User::ROLE_ADMIN){
            $query = $this->request->getQuery();
            if(isset($query["userId"])){
                $games = $gameRepo->findBy([
                    "black_id" => $query["userId"],
                    "white_id" => $query["userId"]
                ], true, $limit, $offset, [["played_date","DESC"]]);
            } else {
                $games = $gameRepo->findByUserName(@$query["name"] ?? "", $limit, $offset, [["played_date","DESC"]]);
            }
        } else {
            $games = $gameRepo->findBy([
                "black_id" => $user->getId(),
                "white_id" => $user->getId()
            ], true, $limit, $offset, [["played_date","DESC"]]);
        }

        $result = [];

        $opponents = [];
        /** @var Game $game */
        foreach ($games as $game) {
            $black_user = $userRepo->findOneBy(["id" => $game->getBId()]);
            $white_user = $userRepo->findOneBy(["id" => $game->getWId()]);

            $result[] = [
                ...$game->jsonSerialize(),
                "black_username" => $black_user->getUserName() ?? "UnknownUser",
                "white_username" => $white_user->getUserName() ?? "UnknownUser",
            ];
        }

        return new JsonResponse($result, 200);
    }

}
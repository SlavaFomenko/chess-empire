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

    #[Route(name: "get_games", path: "/games", methods: [Request::METHOD_GET], roles: User::ROLE_USER_OR_ADMIN)]
    public function getForCurrentUser(): Response
    {
        $userRepo = $this->em->getRepository(User::class);
        $user = $userRepo->find(Context::$security->getData()->id);
        $query = $this->request->getQuery();

        [
            $limit,
            $offset
        ] = $this->pagination(10);

        /** @var GameRepository $gameRepo */
        $gameRepo = $this->em->getRepository(Game::class);

        $count = 0;

        if (Context::$security->getRole() === User::ROLE_ADMIN) {
            $count = $gameRepo->countBy([]);

            if (isset($query["userId"])) {
                $games = $gameRepo->findBy([
                    "black_id" => $query["userId"],
                    "white_id" => $query["userId"]
                ], true, $limit, $offset, [
                    [
                        "played_date",
                        "DESC"
                    ]
                ]);
            } else {
                $games = $gameRepo->findByUserName(@$query["name"] ?? "", $limit, $offset, [
                    [
                        "played_date",
                        "DESC"
                    ]
                ]);
            }
        } else {
            $count = $gameRepo->countBy([
                "black_id" => $user->getId(),
                "white_id" => $user->getId()
            ], true);
            $games = $gameRepo->findBy([
                "black_id" => $user->getId(),
                "white_id" => $user->getId()
            ], true, $limit, $offset, [
                [
                    "played_date",
                    "DESC"
                ]
            ]);
        }

        $result = [];

        $opponents = [];
        /** @var Game $game */
        foreach ($games as $game) {
            $blackUser = $userRepo->find($game->getBlackId());
            $whiteUser = $userRepo->find($game->getWhiteId());

            $result[] = [
                ...$game->jsonSerialize(),
                "black_username" => $blackUser?->getUserName() ?? "UnknownUser",
                "white_username" => $whiteUser?->getUserName() ?? "UnknownUser",
            ];
        }

        if(isset($query['name'])){
            $count = $gameRepo->countByUserName($query['name']);
        }

        return new JsonResponse(['games'=>$result,'pagesCount'=>ceil($count / 10)], 200);
    }

    #[Route(name: "get_game_by_id", path: "/games/{id}", methods: [Request::METHOD_GET], roles: User::ROLE_USER_OR_ADMIN)]
    public function getById(int $id): Response
    {
        $gameRepo = $this->em->getRepository(Game::class);

        /** @var Game $game */
        $game = $gameRepo->find($id);
        if (empty($game)) {
            return new JsonResponse(["message" => "Game not found"], 404);
        }

        $playerIds = [
            $game->getWhiteId(),
            $game->getBlackId()
        ];
        $sec = Context::$security;
        if ($sec->getRole() === User::ROLE_ADMIN || in_array($sec->getData()->id,$playerIds)) {
            $userRepo = $this->em->getRepository(User::class);
            $black = $userRepo->find($game->getBlackId());
            $white = $userRepo->find($game->getWhiteId());
            $gameData = [
                ...$game->jsonSerialize(),
                "black_username" => $black?->getUserName() ?? "UnknownUser",
                "white_username" => $white?->getUserName() ?? "UnknownUser",
                "black_profilePic" => $black?->getProfilePic() ?? "",
                "white_profilePic" => $white?->getProfilePic() ?? "",
            ];
            return new JsonResponse($gameData, 200);
        }

        return new JsonResponse(["message" => "You do not have access to this game"], 403);
    }
}
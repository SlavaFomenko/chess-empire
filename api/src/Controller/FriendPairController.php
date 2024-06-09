<?php

namespace App\Controller;

use App\Entity\FriendPair;
use App\Entity\RatingRange;
use App\Entity\User;
use Handy\Context;
use Handy\Controller\BaseController;
use Handy\Http\JsonResponse;
use Handy\Http\Request;
use Handy\Http\Response;
use Handy\ORM\QueryBuilder;
use Handy\Routing\Attribute\Route;

class FriendPairController extends BaseController
{

    public const FRIEND_PAIRS_PER_PAGE = 10;

    #[Route(name: "friend_pair_create", path: "/friend-pair", methods: [Request::METHOD_POST], roles: User::ROLE_USER_OR_ADMIN)]
    public function create(): Response
    {
        $body = $this->request->getContent();

        if (!isset($body["receiverId"])) {
            return new JsonResponse(["message" => "Missing fields in request body"], 400);
        }

        $receiverId = @(int)$body["receiverId"];

        $userRepo = $this->em->getRepository(User::class);
        $receiver = $userRepo->find($receiverId);
        if (empty($receiver)) {
            return new JsonResponse(["message" => "User not found"], 404);
        }

        $senderId = Context::$security->getData()->id;

        if ($senderId === $receiverId) {
            return new JsonResponse(["message" => "You cannot create a pair with yourself"], 400);
        }

        $qb = new QueryBuilder();
        $qb->from("friend_pair")
            ->orWhere("(sender_id = :sender_id1 AND receiver_id = :receiver_id1)")
            ->orWhere("(sender_id = :receiver_id2 AND receiver_id = :sender_id2)")
            ->setParam([
                "sender_id1"   => $senderId,
                "sender_id2"   => $senderId,
                "receiver_id1" => $receiverId,
                "receiver_id2" => $receiverId
            ]);

        $duplicates = Context::$connection->execute($qb->select(["id"])->getQuery());
        if (!empty($duplicates)) {
            return new JsonResponse(["message" => "Friend pair for this users already exists"], 400);
        }

        $friendPair = new FriendPair();

        $friendPair->setSenderId($senderId)
            ->setReceiverId($receiverId)
            ->setAccepted(false);

        $this->em->persist($friendPair);
        $this->em->flush();

        $friendPairRepo = $this->em->getRepository(FriendPair::class);
        $friendPair = $friendPairRepo->findOneBy([
            "sender_id"   => $senderId,
            "receiver_id" => $receiverId
        ]);

        return new JsonResponse($friendPair, 201);
    }

    #[Route(name: "get_all_friend_pairs", path: "/friend-pair", methods: [Request::METHOD_GET], roles: User::ROLE_USER_OR_ADMIN)]
    public function getAll(): Response
    {
        $userId = Context::$security->getData()->id;

        $query = $this->request->getQuery();

        [
            $limit,
            $offset
        ] = $this->pagination(self::FRIEND_PAIRS_PER_PAGE);

        $accepted = (@$query["accepted"] ?? "true") === "true";

        $qb = new QueryBuilder();
        $qb->from("friend_pair");

        if (isset($query["accepted"])) {
            $qb->andWhere("receiver_id = :user_id")
                ->andWhere("accepted = :accepted")
                ->setParam([
                    "user_id"  => $userId,
                    "accepted" => $accepted
                ]);
        } else {
            $qb->andWhere("(sender_id = :user_id1 OR receiver_id = :user_id2)")
                ->setParam([
                    "user_id1" => $userId,
                    "user_id2" => $userId,
                ]);
        }

        $count = @(int)Context::$connection->execute($qb->select(["COUNT(id)"])->getQuery())[0][0];
        $friendPairs = Context::$connection->execute($qb->select(FriendPair::class)->limit($limit)->offset($offset)->getQuery(), FriendPair::class);

        return new JsonResponse([
            'pagesCount' => ceil($count / self::FRIEND_PAIRS_PER_PAGE),
            "friendPairs"      => $friendPairs
        ], 200);
    }

    #[Route(name: "friend_pair_by_user", path: "/friend-pair/{friendId}", methods: [Request::METHOD_GET], roles: User::ROLE_USER_OR_ADMIN)]
    public function getByUser(int $friendId): Response
    {
        $userId = Context::$security->getData()->id;

        $qb = new QueryBuilder();
        $qb->from("friend_pair")
            ->orWhere("(sender_id = :user_id1 AND receiver_id = :friend_id1)")
            ->orWhere("(sender_id = :friend_id2 AND receiver_id = :user_id2)")
            ->setParam([
                "user_id1"   => $userId,
                "user_id2"   => $userId,
                "friend_id1" => $friendId,
                "friend_id2" => $friendId
            ]);

        $friendPairs = Context::$connection->execute($qb->select(FriendPair::class)->getQuery(), FriendPair::class);
        $friendPair = current($friendPairs);

        if(empty($friendPair)) {
            return new JsonResponse(["message" => "Friend pair not found"], 404);
        }

        return new JsonResponse($friendPair, 200);
    }

    #[Route(name: "friend_pair_accept", path: "/friend-pair/{senderId}", methods: [Request::METHOD_POST], roles: User::ROLE_USER_OR_ADMIN)]
    public function accept(int $senderId): Response
    {
        $receiverId = Context::$security->getData()->id;
        $friendPairRepo = $this->em->getRepository(FriendPair::class);
        $friendPair = $friendPairRepo->findOneBy([
            "sender_id"   => $senderId,
            "receiver_id" => $receiverId
        ]);

        if(empty($friendPair)) {
            return new JsonResponse(["message" => "Friend pair not found"], 404);
        }

        $friendPair->setAccepted(true);

        $this->em->persist($friendPair);
        $this->em->flush();

        return new JsonResponse($friendPair, 201);
    }

    #[Route(name: "friend_pair_delete", path: "/friend-pair/{friendId}", methods: [Request::METHOD_DELETE], roles: User::ROLE_USER_OR_ADMIN)]
    public function delete(int $friendId): Response
    {
        $userId = Context::$security->getData()->id;

        $qb = new QueryBuilder();
        $qb->orWhere("(sender_id = :user_id1 AND receiver_id = :friend_id1)")
            ->orWhere("(sender_id = :friend_id2 AND receiver_id = :user_id2)")
            ->setParam([
                "user_id1"   => $userId,
                "user_id2"   => $userId,
                "friend_id1" => $friendId,
                "friend_id2" => $friendId
            ]);

        $count = (int)Context::$connection->execute($qb->select(["COUNT(id)"])->from("friend_pair")->getQuery())[0][0];

        if($count === 0){
            return new JsonResponse(["message"=>"Friend pair not found"], 404);
        }

        Context::$connection->execute($qb->deleteFrom("friend_pair")->getQuery());

        return new JsonResponse(null, 204);
    }

    function validateEmail($email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    function validatePassword($password): bool
    {
        return preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/', $password) === 1;
    }

    function validateUsername($username): bool
    {
        return preg_match('/^[a-zA-Z0-9]+$/', $username) === 1;
    }

    function validateName($name): bool
    {
        return preg_match('/^[a-zA-Z]+$/', $name) === 1;
    }

}
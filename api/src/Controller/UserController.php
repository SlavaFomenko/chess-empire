<?php

namespace App\Controller;

use App\Entity\User;
use Handy\Controller\BaseController;
use Handy\Http\JsonResponse;
use Handy\Http\Request;
use Handy\Http\Response;
use Handy\Routing\Attribute\Route;

class UserController extends BaseController
{

    #[Route(name: "user_create", path: "/users", methods: [Request::METHOD_POST])]
    public function register(): Response
    {
        $body = $this->request->getContent();

        if (!isset($body["username"],
            $body["first_name"],
            $body["last_name"],
            $body["email"],
            $body["password"])) {
            return new JsonResponse(["message" => "Missing fields in request body"], 400);
        }

        $username = trim($body["username"]);
        $email = trim($body["email"]);
        $password = trim($body["password"]);
        $firstName = trim($body["first_name"]);
        $lastName = trim($body["last_name"]);

        if (!$this->validateEmail($email) ||
            !$this->validateName($firstName) ||
            !$this->validateName($lastName) ||
            !$this->validatePassword($password) ||
            !$this->validateUsername($username)) {
            return new JsonResponse(["message" => "Invalid data"], 400);
        }

        $hashedPassword = hash_hmac('sha256', $password, $_ENV["PASSWORD_HASH_KEY"]);

        $repo = $this->em->getRepository(User::class);
        if (!empty($repo->findBy(["username" => $username]))) {
            return new JsonResponse([
                "message" => "The user with this username already exists"
            ], 400);
        }
        if (!empty($repo->findBy(["email" => $email]))) {
            return new JsonResponse([
                "message" => "The user with this email already exists"
            ], 400);
        }

        $user = new User();

        $user->setEmail($email)
            ->setFirstName($firstName)
            ->setUserName($username)
            ->setLastName($lastName)
            ->setPassword($hashedPassword)
            ->setRating(100)
            ->setRole(User::ROLE_USER);

        $this->em->persist($user);
        $this->em->flush();

        $user = $repo->findOneBy(["email" => $email]);

        return new JsonResponse($user, 201);
    }


    #[Route(name: "get_user_by_id", path: "/users", methods: [Request::METHOD_GET])]
    public function getByID():Response
    {
        $queryParams = $this->request->getQuery();
        if (!isset($queryParams["id"])) {
            return new JsonResponse(["message" => "Missing fields in request query"], 400);
        }
        $repo = $this->em->getRepository(User::class);
        $user = $repo->findOneBy(["id" => $queryParams['id']]);
        if(empty($user)){
            return new JsonResponse(["message" => "User is not defined"], 404);
        }
        return new JsonResponse(['user'=>$user],200);
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
<?php

namespace App\Controller;

use App\Entity\User;
use Handy\Controller\BaseController;
use Handy\Http\JsonResponse;
use Handy\Http\Response;
use Handy\ORM\EntityManager;
use Handy\ORM\QueryBuilder;
use Handy\Routing\Attribute\Route;
use Handy\Security\JWTSecurityProvider;
use mysql_xdevapi\Exception;

class AuthorizationController extends BaseController
{

    #[Route(name: "login_main", path: "/login")]
    public function login(): Response
    {
        return new Response("login");
    }

    #[Route(name: "registration_main", path: "/registration")]
    public function registration(): Response
    {

        $body = $this->request->getContent();

        if (!isset($body["username"],
            $body["first_name"],
            $body["last_name"],
            $body["email"],
            $body["password"])) {
            return new JsonResponse(["message" => "Missing fields in request body"], 400);
        }

        $username = $body["username"];
        $email = $body["email"];
        $password = $body["password"];
        $firstName = $body["first_name"];
        $lastName = $body["last_name"];

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
            ],400);
        }
        if (!empty($repo->findBy(["email" => $email]))) {
            return new JsonResponse([
                "message" => "The user with this email already exists"
            ],400);
        }

        $user = new User();

        $user->setEmail($email)
            ->setFirstName($firstName)
            ->setUserName($username)
            ->setLastName($lastName)
            ->setPassword($hashedPassword)
            ->setRating(0)
            ->setRole('user');

        $this->em->persist($user);
        $this->em->flush();

        $user = $repo->findOneBy(["email" => $email,"username" => $username],true);

        return new JsonResponse($user,201);
    }


    function validateEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    function validatePassword($password)
    {
        return preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/', $password);
    }

    function validateUsername($username)
    {
        return preg_match('/^[a-zA-Z0-9]+$/', $username);
    }

    function validateName($name)
    {
        return preg_match('/^[a-zA-Z ]+$/', $name);
    }

}
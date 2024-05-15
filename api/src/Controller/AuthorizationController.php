<?php

namespace App\Controller;

use App\Entity\User;
use Handy\Controller\BaseController;
use Handy\Http\JsonResponse;
use Handy\Http\Request;
use Handy\Http\Response;
use Handy\Routing\Attribute\Route;
use Handy\Security\JWTSecurityProvider;

class AuthorizationController extends BaseController
{

    #[Route(name: "login", path: "/login-check", methods: [Request::METHOD_POST])]
    public function login(): Response
    {

        $body = $this->request->getContent();

        if (!isset($body['email'], $body['password'])) {
            return new JsonResponse(['message' => 'Missing credentials'], 400);
        }

        $repo = $this->em->getRepository(User::class);

        $user = $repo->findOneBy(['email' => $body['email']]);
        if (empty($user)) {
            return new JsonResponse(['message' => 'Invalid email'], 400);
        }

        $hashedRequestPassword = hash_hmac('sha256', $body['password'], $_ENV["PASSWORD_HASH_KEY"]);
        $hashedPassword = $user->getPassword();

        if ($hashedRequestPassword !== $hashedPassword) {
            return new JsonResponse(['message' => 'Invalid password'], 400);
        }

        $token = JWTSecurityProvider::generateToken([
            "id"       => $user->getId(),
            'username' => $user->getUserName(),
            "role"     => $user->getRole()
        ], 172800);

        return new JsonResponse(["token" => $token], 200);
    }

}
<?php

namespace App\Controller;

use Handy\Controller\BaseController;
use Handy\Http\Response;
use Handy\Routing\Attribute\Route;

class UserController extends BaseController
{

    #[Route(name: "user_main", path: "/user")]
    public function index()
    {
        $this->ctx->response = new Response("User index");
    }

    #[Route(name: "user_by_id", path: "/user/{id}")]
    public function byId(int $id)
    {
        $this->ctx->response = new Response("User with id " . $id);
    }

    #[Route(name: "user_by_name", path: "/user/{name}")]
    public function byName(string $name)
    {
        $this->ctx->response = new Response("User with name " . $name);
    }

    #[Route(name: "comment_by_user_id", path: "/user/{userId}/{commentId}")]
    public function commentById(string $commentId, int $userId)
    {
        $this->ctx->response = new Response("Comment " . $commentId . " for user " . $userId);
    }

}
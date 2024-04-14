<?php

namespace App\Controller;

use Handy\Controller\BaseController;
use Handy\Http\Response;
use Handy\Routing\Attribute\Route;

class NewsController extends BaseController
{
    #[Route(name: "news-main", path: "/news")]
    public function index(){
        $this->ctx->response = new Response("News index");
    }

    #[Route(name: "news-by-id", path: "/news/{id}")]
    public function byId(int $id){
        $this->ctx->response = new Response("News with id " . $id);
    }

    #[Route(name: "news-by-author", path: "/news/{author}")]
    public function byAuthor(string $author){
        $this->ctx->response = new Response("News by " . $author);
    }

    #[Route(name: "comment-by-news-id", path: "/news/{newsId}/{commentId}")]
    public function commentById(string $commentId, int $newsId){
        $this->ctx->response = new Response("Comment " . $commentId . " for news " . $newsId);
    }
}
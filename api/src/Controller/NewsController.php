<?php

namespace App\Controller;

use Handy\Controller\BaseController;
use Handy\Http\Response;
use Handy\Routing\Attribute\Route;
use Handy\Routing\Attribute\RouteFamily;

#[RouteFamily(name: "news", path: "/news")]
class NewsController extends BaseController
{

    #[Route(name: "main", path: "")]
    public function index()
    {
        $this->ctx->response = new Response("News index");
    }

    #[Route(name: "by_id", path: "/{id}")]
    public function byId(int $id)
    {
        $this->ctx->response = new Response("News with id " . $id);
    }

    #[Route(name: "by_author", path: "/{author}")]
    public function byAuthor(string $author)
    {
        $this->ctx->response = new Response("News by " . $author);
    }

    #[Route(name: "comment_by_news_id", path: "news/{newsId}/{commentId}", inFamily: false)]
    public function commentById(string $commentId, int $newsId)
    {
        $this->ctx->response = new Response("Comment " . $commentId . " for news " . $newsId);
    }

}
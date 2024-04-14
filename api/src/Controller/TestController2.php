<?php

namespace App\Controller;

use Handy\Controller\BaseController;
use Handy\Http\Response;
use Handy\Routing\Attribute\Route;

class TestController2 extends BaseController
{
    #[Route(name: "test_index_2", path: "/test-index2/name-{name}/vardump/")]
    public function index(string $name)
    {
        $this->ctx->response = new Response("NAME " . $name);
    }

    public function not_index()
    {
        var_dump("NOT INDEX");
    }
}
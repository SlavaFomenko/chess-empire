<?php

namespace App\Controller;

use Handy\Controller\BaseController;
use Handy\Http\Response;
use Handy\Routing\Attribute\Route;

class TestController extends BaseController
{
    #[Route(name: "test_index", path: "/test-index/{stringParam}/test/{intParam}/{floatParam}/")]
    public function index(string $stringParam, int $intParam, float $floatParam)
    {
        $this->ctx->response = new Response("String " . $stringParam . "<br>Int " . $intParam . "<br>Float " . $floatParam);
    }

    #[Route(name: "not_index", path: "/test")]
    public function not_index()
    {
        $this->ctx->response = new Response("NOT INDEX");
    }
}
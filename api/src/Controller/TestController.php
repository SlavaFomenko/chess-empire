<?php

namespace App\Controller;

use Handy\Controller\BaseController;
use Handy\Routing\Attribute\Route;

class TestController extends BaseController
{
    #[Route(name: "test_index", path: "/test-index")]
    public function index()
    {
        var_dump("INDEX");
    }

    public function not_index()
    {
        var_dump("NOT INDEX");
    }
}
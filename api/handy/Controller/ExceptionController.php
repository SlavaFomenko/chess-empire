<?php

namespace Handy\Controller;

use Handy\Http\Request;
use Handy\Http\Response;

class ExceptionController extends BaseController
{
    public function index(string $id): void
    {
        $this->ctx->response = new Response("Exception!!!" . $this->request->getContent());
    }
}
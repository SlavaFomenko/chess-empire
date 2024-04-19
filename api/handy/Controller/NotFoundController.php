<?php

namespace Handy\Controller;

use Handy\Http\Response;

class NotFoundController extends BaseController
{

    public function index(): void
    {
        $this->ctx->response = new Response("No route found for \"" . $this->request->getMethod() . " " . $this->request->getPath() . "\"");
    }

}
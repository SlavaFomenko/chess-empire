<?php

namespace Handy\Controller;

use Handy\Http\Response;

class ExceptionController extends BaseController
{

    public function index(string $id): Response
    {
        return new Response("Exception!!!" . $this->request->getContent());
    }

}
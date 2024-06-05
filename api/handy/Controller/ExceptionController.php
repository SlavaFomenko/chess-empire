<?php

namespace Handy\Controller;

use Handy\Context;
use Handy\Http\Response;

class ExceptionController extends BaseController
{

    public function index(): Response
    {
        return new Response(Context::$request->getException(), 500);
    }

}
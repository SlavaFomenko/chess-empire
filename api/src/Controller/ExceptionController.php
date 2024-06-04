<?php

namespace App\Controller;

use Handy\Context;
use Handy\Controller\ExceptionController as DefaultExceptionController;
use Handy\Http\JsonResponse;
use Handy\Http\Response;
use Handy\Security\Exception\ForbiddenException;
use Handy\Security\Exception\UnauthorizedException;

class ExceptionController extends DefaultExceptionController
{

    public function index(): Response
    {
        $e = Context::$request->getException();

        if(is_a($e, UnauthorizedException::class)){
            return new JsonResponse(["message" => "Unauthorized"], 401);
        }

        if(is_a($e, ForbiddenException::class)){
            return new JsonResponse(["message" => "Forbidden"], 401);
        }

        return parent::index($e);
    }
}
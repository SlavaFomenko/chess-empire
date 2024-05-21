<?php

namespace Handy\Handling;

use Handy\Context;
use Handy\Http\JsonResponse;
use Handy\Http\Request;


class CorsHandler extends AbstractHandler
{

    public function handle(): void
    {
        header("Access-Control-Allow-Origin: *");
        if(Context::$request->getMethod() == Request::METHOD_OPTIONS){
            Context::$response = new JsonResponse(null, 204);
            header('Access-Control-Allow-Methods: POST, GET, PUT, PATCH, DELETE, OPTIONS');
            header('Access-Control-Allow-Headers: Authorization, Content-Type, Access-Control-Allow-Origin');
            return;
        }
        parent::handle();
    }

}
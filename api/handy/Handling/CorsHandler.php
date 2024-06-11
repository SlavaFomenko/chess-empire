<?php

namespace Handy\Handling;

use Handy\Context;
use Handy\Http\JsonResponse;
use Handy\Http\Request;


class CorsHandler extends AbstractHandler
{

    public function handle(): void
    {
        header("Access-Control-Allow-Origin: " . Context::$config->cors["allow_origin"]);
        if(Context::$request->getMethod() == Request::METHOD_OPTIONS){
            Context::$response = new JsonResponse(null, 204);
            header("Access-Control-Allow-Methods: " . Context::$config->cors["allow_methods"]);
            header("Access-Control-Allow-Headers: " . Context::$config->cors["allow_headers"]);
            return;
        }
        parent::handle();
    }

}
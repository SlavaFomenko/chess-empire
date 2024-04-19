<?php

namespace Handy\Controller;

use Handy\Controller\Exception\EmptyRequestException;
use Handy\Context;
use Handy\Http\Request;

class BaseController
{

    /**
     * @var Context
     */
    protected Context $ctx;

    /**
     * @var Request
     */
    protected Request $request;

    public function __construct(Context $ctx)
    {
        $this->ctx = $ctx;

        if($ctx->request === null){
            throw new EmptyRequestException("Controller called with an empty request object");
        }

        $this->request = $ctx->request;
    }


}
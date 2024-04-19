<?php

namespace Handy\Handling;

use Handy\Context;
use Handy\Http\RequestParser;
use Handy\Routing\Router;

class RouterHandler extends AbstractHandler
{
    public function handle(Context $ctx): void
    {
        Router::handle($ctx);
        parent::handle($ctx);
    }

}
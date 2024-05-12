<?php

namespace Handy\Handling;

use Handy\Routing\Router;

class RouterHandler extends AbstractHandler
{

    public function handle(): void
    {
        Router::handle();
        parent::handle();
    }

}
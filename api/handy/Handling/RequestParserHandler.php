<?php

namespace Handy\Handling;

use Handy\Http\RequestParser;

class RequestParserHandler extends AbstractHandler
{

    public function handle(): void
    {
        RequestParser::parseRequest();
        parent::handle();
    }

}
<?php

namespace Handy\Handling;

use Handy\Context;
use Handy\Http\Exception\UnsupportedRequestException;
use Handy\Http\RequestParser;

class RequestParserHandler extends AbstractHandler
{
    public function handle(Context $ctx): void
    {
        RequestParser::parseRequest($ctx);
        parent::handle($ctx);
    }

}
<?php

namespace ChessFramework\Handling;

use ChessFramework\Context;
use ChessFramework\Http\Exception\UnsupportedRequestException;
use ChessFramework\Http\RequestParser;

class RequestParserHandler extends AbstractHandler
{
    public function handle(Context $ctx): void
    {
        RequestParser::parseRequest($ctx);
        parent::handle($ctx);
    }

}
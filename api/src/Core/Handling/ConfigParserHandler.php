<?php

namespace ChessFramework\Handling;

use ChessFramework\Config\ConfigParser;
use ChessFramework\Context;
use ChessFramework\Handling\AbstractHandler;

class ConfigParserHandler extends AbstractHandler
{
    public function handle(Context $ctx): void
    {
        ConfigParser::parseConfig($ctx);
        parent::handle($ctx);
    }

}
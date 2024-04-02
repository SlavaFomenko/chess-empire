<?php

namespace Handy\Handling;

use Handy\Config\ConfigParser;
use Handy\Context;
use Handy\Handling\AbstractHandler;

class ConfigParserHandler extends AbstractHandler
{
    public function handle(Context $ctx): void
    {
//        ConfigParser::parseUserConfig($ctx);

        ConfigParser::parseConfig($ctx);
        parent::handle($ctx);
    }

}
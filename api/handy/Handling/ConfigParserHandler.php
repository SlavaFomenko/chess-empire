<?php

namespace Handy\Handling;

use Handy\Config\ConfigParser;

class ConfigParserHandler extends AbstractHandler
{

    public function handle(): void
    {
        ConfigParser::parseConfig();
        parent::handle();
    }

}
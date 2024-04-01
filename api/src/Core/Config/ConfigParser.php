<?php

namespace ChessFramework\Config;

use ChessFramework\Context;

class ConfigParser
{
    private const DEFAULT_CONFIG_PATH = "src/Core/Config/";
    private const USER_CONFIG_PATH = "config/";

    public static function parseConfig(Context $ctx): void
    {
        $config = new Config();

        require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/" . self::DEFAULT_CONFIG_PATH . "default_config.php";

        $config->namespaces = $configData["namespaces"];
        $config->controllers = $configData["controllers"];
        $config->supportedContentTypes = $configData["supported_content_types"];
        $config->globalPathPrefix = $configData["global_path_prefix"];

        $ctx->config = $config;
    }
}
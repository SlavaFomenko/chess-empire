<?php

namespace Handy\Config;

use Error;
use Exception;
use Handy\Config\Exception\InvalidConfigException;
use Handy\Config\Exception\InvalidConfigSyntaxException;
use Handy\Context;

class ConfigParser
{

    private const DEFAULT_CONFIG_PATH = "handy/Config/Defaults/";
    private const USER_CONFIG_PATH    = "config/";
    private const CONFIG_FILES        = [
        'namespaces.yaml',
        'config.yaml'
    ];

    public static function getFullDirPath($directory): string
    {
        return dirname($_SERVER["DOCUMENT_ROOT"]) . "/" . $directory;
    }

    public static function parseFromDir(string $targetDirectory): array
    {
        $directory = self::getFullDirPath($targetDirectory);

        $config = array();

        foreach (self::CONFIG_FILES as $file) {
            $filePath = $directory . $file;

            if (!file_exists($filePath)) {
                continue;
            }

            $parsedYaml = @yaml_parse_file($filePath) ?? [];

            if ($parsedYaml === false) {
                throw new InvalidConfigSyntaxException('Invalid syntax in ' . $filePath);
            }

            $config = array_merge($config, $parsedYaml);
        }

        return $config;
    }

    public static function parseConfig(): void
    {
        $configArray = self::parseFromDir(self::DEFAULT_CONFIG_PATH);

        $userConfigDir = self::getFullDirPath(self::USER_CONFIG_PATH);
        if (is_dir(substr($userConfigDir, 0, strrpos($userConfigDir, '/')))) {
            $configArray = array_merge($configArray, self::parseFromDir(self::USER_CONFIG_PATH));
        }

        try {
            $config = new Config();
            $config->namespaces = $configArray["namespaces"];
            $config->controllers = $configArray["controllers"];
            $config->supportedContentTypes = $configArray["supported_content_types"];
            $config->globalPathPrefix = $configArray["global_path_prefix"];
            $config->securityProvider=$configArray["security_provider"];
            $config->cors = $configArray["cors"];
        } catch(Exception|Error $e){
            throw new InvalidConfigException($e->getMessage());
        }

        Context::$config = $config;
    }

}

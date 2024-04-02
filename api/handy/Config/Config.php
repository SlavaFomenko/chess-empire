<?php

namespace Handy\Config;

class Config
{

    /**
     * Configured namespaces
     * @var array
     */
    public array $namespaces;

    /**
     * Default controllers
     * @var array
     */
    public array $controllers;

    /**
     * Supported request content types
     * @var array
     */
    public array $supportedContentTypes;

    /**
     * Global path prefix
     * @var string
     */
    public string $globalPathPrefix;

}
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
     * CORS settings: allow origin, allow methods, allow headers
     * @var array
     */
    public array $cors;

    /**
     * Global path prefix
     * @var string
     */
    public string $globalPathPrefix;

    /**
     * @var string
     */
    public string $securityProvider;
}
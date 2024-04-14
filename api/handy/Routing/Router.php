<?php

namespace Handy\Routing;

use Handy\Context;
use Handy\Controller\BaseController;
use Handy\Routing\Attribute\Route as RouteAttribute;
use Handy\Routing\Exception\ControllerDirectoryNotFoundException;
use Handy\Routing\Exception\InvalidMethodArgumentsException;
use Handy\Routing\Exception\UnsupportedParamTypeException;
use ReflectionClass;

class Router
{

    public static function handle(Context $ctx): void
    {
        $namespaces = array_filter($ctx->config->namespaces, function ($item) {
            return $item["type"] == "controller";
        });

        $routes = [];


        foreach ($namespaces as $namespace => $data) {
            $routes = array_merge($routes, RouteParser::getNamespaceRoutes($namespace, $data["path"]));
        }

        foreach ($routes as $route) {
            if (preg_match($route->getPathRegex(), $ctx->request->getPath()) === 1) {
                $route->execute($ctx);
                return;
            }
        }
    }
}
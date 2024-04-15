<?php

namespace Handy\Routing;

use Handy\Context;
use Handy\Routing\Exception\ControllerDirectoryNotFoundException;
use Handy\Routing\Exception\DuplicateParamNameException;
use Handy\Routing\Exception\DuplicateRouteNameException;
use Handy\Routing\Exception\InvalidMethodArgumentsException;
use Handy\Routing\Exception\UnsupportedParamTypeException;

class Router
{

    /**
     * @param Context $ctx
     * @return void
     * @throws DuplicateRouteNameException
     * @throws ControllerDirectoryNotFoundException
     * @throws DuplicateParamNameException
     * @throws InvalidMethodArgumentsException
     * @throws UnsupportedParamTypeException
     * @throws \ReflectionException
     */
    public static function handle(Context $ctx): void
    {
        $namespaces = array_filter($ctx->config->namespaces, function ($item) {
            return $item["type"] == "controller";
        });

        $routes = RouteParser::getRoutes($namespaces);

        foreach ($routes as $route) {
            if (preg_match($route->getPathRegex(), $ctx->request->getPath()) === 1 && in_array($ctx->request->getMethod(), $route->getMethods())) {
                $route->execute($ctx);
                return;
            }
        }

        $notFoundController = $ctx->config->controllers["NotFound"];
        $method = $notFoundController["method"];
        $notFoundControllerInstance = new $notFoundController["controller"]($ctx);
        $notFoundControllerInstance->$method();
    }

}
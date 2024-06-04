<?php

namespace Handy\Routing;

use Exception;
use Handy\Context;
use Handy\Controller\BaseController;
use Handy\Exception\DirectoryNotFoundException;
use Handy\Http\Response;
use Handy\Routing\Attribute\Route as RouteAttribute;
use Handy\Routing\Attribute\RouteFamily;
use Handy\Routing\Exception\DuplicateRouteNameException;
use Handy\Routing\Exception\InvalidMethodArgumentsException;
use Handy\Routing\Exception\InvalidResponseReturnedException;
use Handy\Security\Exception\UnauthorizedException;
use Handy\Routing\Exception\UnsupportedParamTypeException;
use Handy\Security\Security;
use Handy\Utils\Resolver;
use ReflectionException;
use ReflectionMethod;

class Router
{

    /**
     * @throws UnauthorizedException
     * @throws DuplicateRouteNameException
     * @throws InvalidMethodArgumentsException
     * @throws UnsupportedParamTypeException
     * @throws ReflectionException|DirectoryNotFoundException|InvalidResponseReturnedException
     */
    public static function handle(): void
    {
        $namespaces = array_filter(Context::$config->namespaces, function ($item) {
            return $item["type"] == "controller";
        });

        $routes = self::parseRoutes($namespaces);

        $notFoundConfig = Context::$config->controllers["NotFound"];
        $notFoundRoute = new Route();
        $notFoundRoute->setName("handy-not-found")
            ->setController($notFoundConfig["controller"])
            ->setMethod($notFoundConfig["method"])
            ->setPath("")
            ->setPathRegex("/.*/");

        $routes["handy-not-found"] = $notFoundRoute;

        foreach ($routes as $route) {
            if (preg_match($route->getPathRegex(), Context::$request->getPath()) === 1 && in_array(Context::$request->getMethod(), $route->getMethods()) && self::haveAccess($route)) {
                $response = $route->execute();
                if (!is_a($response, Response::class, true)) {
                    throw new InvalidResponseReturnedException("Invalid response type returned: " . $response::class);
                }
                Context::$response = $response;
                return;
            }
        }
    }

    public static function handleException(): void
    {
        try {
            $exceptionConfig = Context::$config->controllers["Exception"];
            $exceptionRoute = new Route();
            $exceptionRoute->setName("handy-exception")
                ->setController($exceptionConfig["controller"])
                ->setMethod($exceptionConfig["method"])
                ->setPath("")
                ->setPathRegex("/.*/");

            Context::$response = $exceptionRoute->execute();
        } catch (Exception $newE){
            Context::$response = new Response($newE, 500);
        }
    }

    public static function haveAccess($route): bool
    {
        if (empty($route->getRoles())) {
            return true;
        }

        $sec = Context::$security;
        if ($sec->getRole() === Security::ROLE_UNAUTHORIZED) {
            throw new UnauthorizedException("Unauthorized", 401);
        }
        if (!in_array($sec->getRole(), $route->getRoles())) {
            throw new UnauthorizedException("Forbidden", 403);
        }

        return true;
    }

    /**
     * @param $namespaces
     * @return array
     * @throws UnauthorizedException
     * @throws DuplicateRouteNameException
     * @throws InvalidMethodArgumentsException
     * @throws ReflectionException
     * @throws UnsupportedParamTypeException
     * @throws DirectoryNotFoundException
     */
    public static function parseRoutes($namespaces): array
    {
        $methods = [];
        foreach ($namespaces as $namespace => $data) {
            array_push($methods, ...Resolver::getMethodsInNamespace($namespace, $data["path"], BaseController::class, [RouteAttribute::class]));
        }

        $routes = [];

        /** @var ReflectionMethod $method */
        foreach ($methods as $method) {
            $routes = array_merge_recursive($routes, self::parseMethodRoutes($method));
            $duplicates = array_filter($routes, fn($route) => is_array($route));
            if (!empty($duplicates)) {
                $duplicate = array_values($duplicates)[0];
                self::duplicateRouteException($duplicate["name"][0], $duplicate["controller"][0]);
            }
        }

        uasort($routes, fn($r1, $r2) => strcmp($r1->getPriorityPath(), $r2->getPriorityPath()));

        return $routes;
    }

    /**
     * @param ReflectionMethod $method
     * @return array
     * @throws UnauthorizedException
     * @throws DuplicateRouteNameException
     * @throws InvalidMethodArgumentsException
     * @throws UnsupportedParamTypeException
     */
    public static function parseMethodRoutes(ReflectionMethod $method): array
    {
        $methodRoutes = [];

        $controller = $method->getDeclaringClass();

        $routeFamily = [
            "name" => "",
            "path" => ""
        ];

        $routeFamilyAttributes = array_filter($controller->getAttributes(), fn($a) => $a->getName() === RouteFamily::class);
        if (isset($routeFamilyAttributes[0])) {
            $routeFamilyInstance = $routeFamilyAttributes[0]->newInstance();
            $routeFamily["path"] = "/" . ltrim(rtrim($routeFamilyInstance->getPath(), "/"), "/");
            $routeFamily["name"] = $routeFamilyInstance->getName() . "_";
        }

        $attributes = array_filter($method->getAttributes(), fn($a) => $a->getName() === RouteAttribute::class);
        foreach ($attributes as $attribute) {
            $routeData = $attribute->newInstance();

            $routePath = "/" . ltrim($routeData->getPath(), "/");
            $path = rtrim(($routeData->isInFamily() ? $routeFamily["path"] : "") . $routePath, "/") . "/";

            $paramRegex = '/{([^}]+)}/';
            preg_match_all($paramRegex, $path, $matches);

            $paramsCount = array_count_values($matches[1]);

            $params = [];
            $pathRegex = "/^" . preg_quote($path, '/') . "$/";
            foreach ($matches[1] as $param) {
                if ($paramsCount[$param] > 1) {
                    throw new UnauthorizedException("Duplicate entry for param \"" . $param . "\" in path \"" . $path . "\"");
                }

                $args = array_values(array_filter($method->getParameters(), fn($p) => $p->getName() === $param));
                if (empty($args)) {
                    throw new InvalidMethodArgumentsException("Argument for param \"" . $param . "\" not found in " . $method->getName() . "() method");
                }

                $arg = $args[0];
                $argType = $arg->getType()?->getName() ?: "no type";
                if (!in_array($argType, Route::SUPPORTED_PARAM_TYPES)) {
                    throw new UnsupportedParamTypeException("Unsupported type \"" . $argType . "\" for param \"" . $param . "\" in " . $method->getName() . "() method");
                }

                $typeRegex = Route::PARAM_TYPES_REGEXPS[$argType];
                $pathRegex = str_replace("\{" . $param . "\}", "(" . $typeRegex . ")", $pathRegex);

                $params[] = [
                    $param,
                    $argType
                ];
            }

            $routeName = ($routeData->isInFamily() ? $routeFamily["name"] : "") . $routeData->getName();

            if (isset($methodRoutes[$routeName])) {
                self::duplicateRouteException($routeName, $controller->getName());
            }

            $route = new Route();
            $route->setName($routeName)
                ->setPath($path)
                ->setPathRegex($pathRegex)
                ->setParams($params)
                ->setMethods($routeData->getMethods())
                ->setRoles($routeData->getRoles())
                ->setController($controller->getName())
                ->setMethod($method->getName());

            $methodRoutes[$routeName] = $route;
        }

        return $methodRoutes;
    }

    /**
     * @param string $name
     * @param string $controller
     * @return void
     * @throws DuplicateRouteNameException
     */
    public static function duplicateRouteException(string $name, string $controller): void
    {
        $message = "Duplicate definitions for route \"" . $name . "\" found in " . $controller;
        throw new DuplicateRouteNameException($message);
    }

}
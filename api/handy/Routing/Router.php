<?php

namespace Handy\Routing;

use Handy\Context;
use Handy\Controller\BaseController;
use Handy\Http\Response;
use Handy\Routing\Attribute\Route as RouteAttribute;
use Handy\Routing\Exception\ControllerDirectoryNotFoundException;
use Handy\Routing\Exception\InvalidMethodArgumentsException;
use Handy\Routing\Exception\UnsupportedParamTypeException;
use ReflectionClass;
use ReflectionMethod;

class Router
{

    public static function handle(Context $ctx): void
    {
        $namespaces = array_filter($ctx->config->namespaces, function ($item) {
            return $item["type"] == "controller";
        });

        $routes = [];

        foreach ($namespaces as $namespace => $data) {
            $routes = array_merge($routes, self::scanNamespaceForRoutes($namespace, $data["path"]));
        }

        var_dump($ctx->request->getPath());

        /** @var Route $route */
        foreach ($routes as $route) {
            if (preg_match($route->getPathRegex(), $ctx->request->getPath()) === 1) {
                $route->execute($ctx);
                return;
            }
        }
    }

    public static function scanNamespaceForRoutes(string $namespace, string $path): array
    {
        $controllers = self::scanForControllers($namespace, $path);

        $routes = [];

        foreach ($controllers as $controller) {
            $routes = array_merge($routes, self::scanForRoutes($controller));
        }

        return $routes;
    }

    public static function scanForRoutes(string $controller): array
    {
        $routes = [];

        $reflectionClass = new ReflectionClass($controller);
        $methods = $reflectionClass->getMethods();
        foreach ($methods as $method) {
            $attributes = array_filter($method->getAttributes(), fn($a) => $a->getName() === RouteAttribute::class);

            foreach ($attributes as $attribute) {
                $routeData = $attribute->newInstance();

                $path = $routeData->getPath();
                if (!str_ends_with($path, "/")) {
                    $path .= "/";
                }

                $paramRegex = '/{([^}]+)}/';
                preg_match_all($paramRegex, $path, $matches);
                $parts = preg_split($paramRegex, $path);

                $params = [];
                $pathRegex = "/^" . preg_quote($path, '/') . "$/";
                foreach ($parts as $index => $part) {
                    if (!isset($matches[1][$index])) {
                        continue;
                    }

                    $param = $matches[1][$index];
                    $args = array_values(array_filter($method->getParameters(), fn($p) => $p->getName() === $param));
                    if (empty($args)) {
                        throw new InvalidMethodArgumentsException("Argument for param \"" . $param . "\" not found in " . $method->getName() . "() method");
                    }

                    $arg = $args[0];
                    $argType = $arg->getType()->getName();
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

                $route = new Route();
                $route->setPath($path)
                    ->setPathRegex($pathRegex)
                    ->setParams($params)
                    ->setMethods($routeData->getMethods())
                    ->setController($controller)
                    ->setMethod($method->getName());

                $routes[$routeData->getName()] = $route;
            }
        }

        return $routes;
    }

    public static function scanForControllers(string $namespace, string $path): array
    {
        $directory = dirname($_SERVER["DOCUMENT_ROOT"]) . "/" . $path;

        if (!is_dir($directory)) {
            throw new ControllerDirectoryNotFoundException("Directory \"" . $directory . "\" does not exist");
        }

        $classes = [];
        $files = scandir($directory);

        foreach ($files as $file) {
            if (in_array($file, [
                ".",
                ".."
            ])) {
                continue;
            }

            if (!(is_file($directory . '/' . $file) && pathinfo($file, PATHINFO_EXTENSION) === 'php')) {
                continue;
            }

            $class = $namespace . '\\' . pathinfo($file, PATHINFO_FILENAME);;

            if (class_exists($class) && is_subclass_of($class, BaseController::class)) {
                $classes[] = $class;
            }
        }

        return $classes;
    }

}
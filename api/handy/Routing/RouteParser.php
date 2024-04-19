<?php

namespace Handy\Routing;

use Handy\Controller\BaseController;
use Handy\Routing\Attribute\Route as RouteAttribute;
use Handy\Routing\Attribute\RouteFamily;
use Handy\Routing\Exception\ControllerDirectoryNotFoundException;
use Handy\Routing\Exception\DuplicateParamNameException;
use Handy\Routing\Exception\DuplicateRouteNameException;
use Handy\Routing\Exception\InvalidMethodArgumentsException;
use Handy\Routing\Exception\UnsupportedParamTypeException;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

class RouteParser
{

    /**
     * @param $namespaces
     * @return array
     * @throws ControllerDirectoryNotFoundException
     * @throws DuplicateParamNameException
     * @throws DuplicateRouteNameException
     * @throws InvalidMethodArgumentsException
     * @throws ReflectionException
     * @throws UnsupportedParamTypeException
     */
    public static function getRoutes($namespaces): array
    {
        $routes = [];

        foreach ($namespaces as $namespace => $data) {
            $namespaceRoutes = RouteParser::getNamespaceRoutes($namespace, $data["path"]);

            $duplicates = array_intersect_key($namespaceRoutes, $routes);

            if (!empty($duplicates)) {
                throw new DuplicateRouteNameException("Duplicate definitions for route \"" . array_keys($duplicates)[0] . "\" found in " . array_values($duplicates)[0]->getController());
            }

            $routes = array_merge($routes, $namespaceRoutes);
        }

        uasort($routes, fn($r1, $r2) => strcmp($r1->getPriorityPath(), $r2->getPriorityPath()));

        return $routes;
    }

    /**
     * @param string $namespace
     * @param string $path
     * @return array
     * @throws ControllerDirectoryNotFoundException
     * @throws DuplicateParamNameException
     * @throws DuplicateRouteNameException
     * @throws InvalidMethodArgumentsException
     * @throws ReflectionException
     * @throws UnsupportedParamTypeException
     */
    public static function getNamespaceRoutes(string $namespace, string $path): array
    {
        $routes = [];

        $controllers = self::getControllersInNamespace($namespace, $path);
        foreach ($controllers as $controller) {
            $controllerRoutes = self::getControllerRoutes($controller);
            $duplicates = array_intersect_key($routes, $controllerRoutes);

            if (!empty($duplicates)) {
                throw new DuplicateRouteNameException("Duplicate definitions for route \"" . array_keys($duplicates)[0] . "\" found in " . $controller);
            }

            $routes = array_merge($routes, $controllerRoutes);
        }

        return $routes;
    }

    /**
     * @param ReflectionClass $controller
     * @param ReflectionMethod $method
     * @return array
     * @throws DuplicateParamNameException
     * @throws DuplicateRouteNameException
     * @throws InvalidMethodArgumentsException
     * @throws UnsupportedParamTypeException
     */
    public static function getMethodRoutes(ReflectionClass $controller, ReflectionMethod $method): array
    {
        $methodRoutes = [];

        $routeFamilyPath = "";
        $routeFamilyName = "";

        $routeFamily = array_filter($controller->getAttributes(), fn($a) => $a->getName() === RouteFamily::class);
        if (isset($routeFamily[0])) {
            $routeFamilyInstance = $routeFamily[0]->newInstance();
            $routeFamilyPath = "/" . ltrim(rtrim($routeFamilyInstance->getPath(), "/"), "/");
            $routeFamilyName = $routeFamilyInstance->getName() . "-";
        }

        $attributes = array_filter($method->getAttributes(), fn($a) => $a->getName() === RouteAttribute::class);
        foreach ($attributes as $attribute) {
            $routeData = $attribute->newInstance();

            $routePath = "/" . ltrim($routeData->getPath(), "/");
            $path = rtrim(($routeData->isInFamily() ? $routeFamilyPath : "") . $routePath, "/") . "/";

            $paramRegex = '/{([^}]+)}/';
            preg_match_all($paramRegex, $path, $matches);

            $paramsCount = array_count_values($matches[1]);

            $params = [];
            $pathRegex = "/^" . preg_quote($path, '/') . "$/";
            foreach ($matches[1] as $param) {
                if ($paramsCount[$param] > 1) {
                    throw new DuplicateParamNameException("Duplicate entry for param \"" . $param . "\" in path \"" . $path . "\"");
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

            $route = new Route();
            $route->setPath($path)
                ->setPathRegex($pathRegex)
                ->setParams($params)
                ->setMethods($routeData->getMethods())
                ->setController($controller->getName())
                ->setMethod($method->getName());

            if (isset($methodRoutes[$routeData->getName()])) {
                throw new DuplicateRouteNameException("Duplicate definitions for route \"" . $routeData->getName() . "\" found in " . $controller);
            }
            $methodRoutes[($routeData->isInFamily() ? $routeFamilyName : "") . $routeData->getName()] = $route;
        }

        return $methodRoutes;
    }

    /**
     * @param string $controller
     * @return array
     * @throws DuplicateParamNameException
     * @throws DuplicateRouteNameException
     * @throws InvalidMethodArgumentsException
     * @throws UnsupportedParamTypeException
     * @throws ReflectionException
     */
    public static function getControllerRoutes(string $controller): array
    {
        $controllerRoutes = [];
        $reflectionClass = new ReflectionClass($controller);

        $methods = $reflectionClass->getMethods();
        foreach ($methods as $method) {
            $methodRoutes = self::getMethodRoutes($reflectionClass, $method);

            $duplicates = array_intersect_key($controllerRoutes, $methodRoutes);
            if (!empty($duplicates)) {
                throw new DuplicateRouteNameException("Duplicate definitions for route \"" . array_keys($duplicates)[0] . "\" found in " . $controller);
            }

            $controllerRoutes = array_merge($controllerRoutes, $methodRoutes);
        }

        return $controllerRoutes;
    }

    /**
     * @param string $namespace
     * @param string $path
     * @return array
     * @throws ControllerDirectoryNotFoundException
     */
    public static function getControllersInNamespace(string $namespace, string $path): array
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

            $class = $namespace . '\\' . pathinfo($file, PATHINFO_FILENAME);

            if (class_exists($class) && is_subclass_of($class, BaseController::class)) {
                $classes[] = $class;
            }
        }

        return $classes;
    }

}
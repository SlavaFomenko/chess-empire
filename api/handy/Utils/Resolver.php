<?php

namespace Handy\Utils;

use Handy\Exception\DirectoryNotFoundException;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionProperty;

class Resolver
{

    /**
     * @param string $namespace
     * @param string $path
     * @param string|null $baseClass
     * @param array $requiredAttributes
     * @return array
     * @throws DirectoryNotFoundException|ReflectionException
     */
    public static function getMethodsInNamespace(string $namespace, string $path, ?string $baseClass = null, array $requiredAttributes = []): array
    {
        $methods = [];

        foreach (self::getClassesInNamespace($namespace, $path, $baseClass) as $class) {
            array_push($methods, ...self::getMethodsInClass($class, $requiredAttributes));
        }

        return $methods;
    }

    /**
     * @param string $class
     * @param array $requiredAttributes
     * @return array
     * @throws ReflectionException
     */
    public static function getMethodsInClass(string $class, array $requiredAttributes = []): array
    {
        $reflectionClass = new ReflectionClass($class);

        return array_filter($reflectionClass->getMethods(), fn($rm) => self::checkForAttributes($rm, $requiredAttributes));
    }

    /**
     * @param string $class
     * @param array $requiredAttributes
     * @return array
     * @throws ReflectionException
     */
    public static function getPropsInClass(string $class, array $requiredAttributes = []): array
    {
        $reflectionClass = new ReflectionClass($class);

        return array_filter($reflectionClass->getProperties(), fn($rp) => self::checkForAttributes($rp, $requiredAttributes));
    }

    /**
     * @param ReflectionMethod|ReflectionProperty $reflection
     * @param array $requiredAttributes
     * @return bool
     */
    public static function checkForAttributes(ReflectionMethod|ReflectionProperty $reflection, array $requiredAttributes): bool
    {
        $attributeNames = array_map(fn($ra) => $ra->getName(), $reflection->getAttributes());
        $filteredAttributes = array_filter($requiredAttributes, fn($a) => in_array($a, $attributeNames));

        return count($requiredAttributes) === count($filteredAttributes);
    }

    /**
     * @param string $namespace
     * @param string $path
     * @param string|null $baseClass
     * @return array
     * @throws DirectoryNotFoundException
     */
    public static function getClassesInNamespace(string $namespace, string $path, ?string $baseClass = null): array
    {
        $directory = dirname($_SERVER["DOCUMENT_ROOT"]) . "/" . $path;

        if (!is_dir($directory)) {
            throw new DirectoryNotFoundException("Directory \"" . $directory . "\" does not exist");
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

            if (class_exists($class) && ($baseClass === null || is_subclass_of($class, $baseClass))) {
                $classes[] = $class;
            }
        }

        return $classes;
    }

}
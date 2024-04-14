<?php

namespace Handy\Routing;

use Handy\Context;
use Handy\Routing\Exception\UnsupportedParamTypeException;
use ReflectionMethod;

class Route
{
    public const SUPPORTED_PARAM_TYPES = ["string", "int", "float"];
    public const PARAM_TYPES_REGEXPS = [
        "string" => "[^\/]+",
        "int" => "\d+",
        "float" => "\d+(?:\,\d+)?"
    ];

    /**
     * Route path
     * @var string
     */
    public string $path;

    /**
     * Route path
     * @var string
     */
    public string $pathRegex;

    /**
     * URL params
     * @var array
     */
    public array $params;

    /**
     * Route supported methods
     * @var array
     */
    public array $methods;

    /**
     * Route controller
     * @var string
     */
    public string $controller;

    /**
     * Route method in controller
     * @var string
     */
    public string $method;

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     * @return self
     */
    public function setPath(string $path): self
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @return string
     */
    public function getPathRegex(): string
    {
        return $this->pathRegex;
    }

    /**
     * @param string $pathRegex
     * @return self
     */
    public function setPathRegex(string $pathRegex): self
    {
        $this->pathRegex = $pathRegex;
        return $this;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @param array $params
     * @return self
     */
    public function setParams(array $params): self
    {
        $this->params = $params;
        return $this;
    }

    /**
     * @return array
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * @param array $methods
     * @return self
     */
    public function setMethods(array $methods): self
    {
        $this->methods = $methods;
        return $this;
    }

    /**
     * @return string
     */
    public function getController(): string
    {
        return $this->controller;
    }

    /**
     * @param string $controller
     * @return self
     */
    public function setController(string $controller): self
    {
        $this->controller = $controller;
        return $this;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @param string $method
     * @return self
     */
    public function setMethod(string $method): self
    {
        $this->method = $method;
        return $this;
    }

    public function parseParam(array $param, $value): mixed
    {
        return match ($param[1]) {
            "string" => (string)$value,
            "int" => (int)$value,
            "float" => (float)str_replace(",", ".", $value),
            default => throw new UnsupportedParamTypeException("Unsupported type \"" . $param[1] . "\" for param \"" . $param[0] . "\""),
        };
    }

    public function prepareParams(string $url): array
    {
        $result = [];

        preg_match_all($this->getPathRegex(), $url, $matches);

        foreach ($this->getParams() as $index=>$param) {
            $result[$param[0]] = $this->parseParam($param, $matches[$index+1][0]);
        }

        return $result;
    }

    public function execute(Context $ctx): void
    {
        $controller = $this->getController();
        $method = $this->getMethod();
        $controllerInstance = new $controller($ctx);
        $reflectionMethod = new ReflectionMethod($controller, $method);
        $reflectionMethod->invokeArgs($controllerInstance, $this->prepareParams($ctx->request->getPath()));
    }

}
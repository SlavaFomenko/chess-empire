<?php

namespace Handy;

use Exception;
use Handy\Handling\ConfigParserHandler;
use Handy\Handling\CorsHandler;
use Handy\Handling\OrmHandler;
use Handy\Handling\RequestParserHandler;
use Handy\Handling\RouterHandler;
use Handy\Handling\SecurityHandler;
use Handy\Routing\Router;


class Core
{

    /**
     * Handlers
     * @var array
     */
    public array $handlers;

    public function __construct()
    {
        $this->handlers = [
            new ConfigParserHandler(),
            new RequestParserHandler(),
            new CorsHandler(),
            new OrmHandler(),
            new SecurityHandler(),
            new RouterHandler()
        ];

        foreach ($this->handlers as $index => $handler) {
            if ($index < count($this->handlers) - 1)
                $handler->setNext($this->handlers[$index + 1]);
        }
    }

    public function handleException(Exception $e): void
    {
        Context::$request->setException($e);
        Router::handleException();
    }

    /**
     * @return string
     */
    public function handle(): string
    {
        try {
            $this->handlers[0]->handle();
        } catch (Exception $e) {
            $this->handleException($e);
        }
        return (string)Context::$response;
    }

}
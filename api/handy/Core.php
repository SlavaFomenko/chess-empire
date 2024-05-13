<?php

namespace Handy;

use Exception;
use Handy\Handling\ConfigParserHandler;
use Handy\Handling\OrmHandler;
use Handy\Handling\RequestParserHandler;
use Handy\Handling\RouterHandler;
use Handy\Handling\SecurityHandler;


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
            new OrmHandler(),
            new SecurityHandler(),
            new RouterHandler()
        ];

        foreach ($this->handlers as $index => $handler) {
            if ($index < count($this->handlers) - 1)
                $handler->setNext($this->handlers[$index + 1]);
        }
    }

    /**
     * @return string
     */
    public function handle(): string
    {
        try {
            $this->handlers[0]->handle();
            return Context::$response ?? "Null Response";
        } catch (Exception $e) {
            return $e;
        }
    }

}
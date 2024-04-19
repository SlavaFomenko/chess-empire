<?php

namespace Handy;

use Exception;
use Handy\Handling\ConfigParserHandler;
use Handy\Handling\RequestParserHandler;
use Handy\Handling\RouterHandler;


class Core
{

    /**
     * Context instance
     * @var Context
     */
    public Context $ctx;

    /**
     * Handlers
     * @var array
     */
    public array $handlers;

    public function __construct()
    {
        $this->ctx = new Context();

        $this->handlers = [
            new ConfigParserHandler(),
            new RequestParserHandler(),
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
            $this->handlers[0]->handle($this->ctx);
            return $this->ctx->response ?? "Null Response";
        } catch (Exception $e) {
            return $e;
        }
    }

}
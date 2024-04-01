<?php

namespace ChessFramework;

use ChessFramework\Handling\ConfigParserHandler;
use ChessFramework\Handling\RequestParserHandler;
use Exception;

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
            new RequestParserHandler()
        ];

        foreach ($this->handlers as $index => $handler) {
            if ($index < count($this->handlers) - 1)
                $handler->setNext($this->handlers[$index + 1]);
        }
    }

    public function handle(): string
    {
        try{
            $this->handlers[0]->handle($this->ctx);
            return $this->ctx->response ?? "Null Response";
        } catch (Exception $e){
            return $e;
        }
    }

}
<?php

namespace Handy\Handling;

abstract class AbstractHandler implements Handler
{

    /**
     * Next handler in the chain
     * @var ?Handler
     */
    private ?Handler $next;

    public function __construct()
    {
        $this->next = null;
    }

    public function setNext(Handler $handler): Handler
    {
        $this->next = $handler;
        return $this;
    }

    public function handle(): void
    {
        $this->next?->handle();
    }

}
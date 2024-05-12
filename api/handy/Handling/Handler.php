<?php

namespace Handy\Handling;

use Exception;

interface Handler
{

    /**
     * Sets next handler in the chain
     * @param Handler $handler
     * @return Handler
     */
    public function setNext(Handler $handler): Handler;

    /**
     * Handles request step
     * @return void
     * @throws Exception
     */
    public function handle(): void;

}
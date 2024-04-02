<?php

namespace Handy\Handling;

use Handy\Context;
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
     * @throws Exception
     * @param Context $ctx
     * @return void
     */
    public function handle(Context $ctx): void;

}
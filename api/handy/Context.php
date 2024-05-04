<?php

namespace Handy;

use Handy\Config\Config;
use Handy\Http\Request;
use Handy\Http\Response;
use Handy\Security\Security;

class Context
{

    /**
     * @var ?Config
     */
    public ?Config $config;

    /**
     * Current request
     * @var ?Request
     */
    public ?Request $request;

    /**
     * Current response
     * @var ?Response
     */
    public ?Response $response;

    /**
     * @var Security|null
     */
    public ?Security $security;

    public function __construct()
    {
        $this->config = null;
        $this->request = null;
        $this->response = null;
        $this->security = null;
    }
}
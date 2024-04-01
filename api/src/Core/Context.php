<?php

namespace ChessFramework;

use ChessFramework\Config\Config;
use ChessFramework\Http\Request;
use ChessFramework\Http\Response;

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


    public function __construct()
    {
        $this->config = null;
        $this->request = null;
        $this->response = null;
    }
}
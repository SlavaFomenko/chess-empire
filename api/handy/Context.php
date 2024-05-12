<?php

namespace Handy;

use Handy\Config\Config;
use Handy\Http\Request;
use Handy\Http\Response;
use Handy\ORM\Connection;
use Handy\ORM\EntityManager;
use Handy\Security\Security;

class Context
{

    /**
     * @var ?Config
     */
    public static ?Config $config = null;

    /**
     * Current request
     * @var ?Request
     */
    public static ?Request $request = null;

    /**
     * Current response
     * @var ?Response
     */
    public static ?Response $response = null;

    /**
     * @var ?Connection
     */
    public static ?Connection $connection = null;

    /**
     * @var ?EntityManager
     */
    public static ?EntityManager $entityManager = null;

    /**
     * @var ?Security
     */
    public static ?Security $security;

}
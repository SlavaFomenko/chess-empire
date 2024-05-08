<?php

namespace Handy;

use Handy\Config\Config;
use Handy\Http\Request;
use Handy\Http\Response;
use Handy\ORM\Connection;
use Handy\ORM\EntityManager;

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
     * @var Connection|null
     */
    public static ?Connection $connection = null;

    /**
     * @var EntityManager|null
     */
    public static ?EntityManager $entityManager = null;

}
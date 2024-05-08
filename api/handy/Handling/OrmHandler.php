<?php

namespace Handy\Handling;

use Handy\Context;
use Handy\ORM\Connection;
use Handy\ORM\EntityManager;

class OrmHandler extends AbstractHandler
{

    public function handle(): void
    {
        Context::$connection = new Connection();
        Context::$connection->connect();
        Context::$entityManager = new EntityManager();

        parent::handle();
    }

}
<?php

namespace Handy\Controller;

use Handy\Context;
use Handy\Controller\Exception\EmptyRequestException;
use Handy\Http\Request;
use Handy\ORM\EntityManager;

class BaseController
{

    /**
     * @var Request
     */
    protected Request $request;

    /**
     * @var EntityManager|null
     */
    protected ?EntityManager $em;

    public function __construct()
    {
        if (Context::$request === null) {
            throw new EmptyRequestException("Controller called with an empty request object");
        }

        $this->request = Context::$request;
        $this->em = Context::$entityManager;
    }

}
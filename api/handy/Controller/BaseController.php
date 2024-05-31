<?php

namespace Handy\Controller;

use Handy\Context;
use Handy\Controller\Exception\EmptyRequestException;
use Handy\Http\Request;
use Handy\ORM\EntityManager;

class BaseController
{

    public const ITEMS_PER_PAGE = 20;

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

    public function pagination($itemsPerPage = self::ITEMS_PER_PAGE): array
    {
        $query = $this->request->getQuery();
        $page = max(1, @$query["page"]);
        $offset = ($page - 1) * $itemsPerPage;
        return [$itemsPerPage, $offset, $page];
    }

}
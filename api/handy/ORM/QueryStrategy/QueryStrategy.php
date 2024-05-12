<?php

namespace Handy\ORM\QueryStrategy;

use Handy\ORM\Query;

interface QueryStrategy
{
    public function getSQL(Query $q): string;
}
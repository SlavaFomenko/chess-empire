<?php

namespace Handy\ORM;

interface ArrayMappable
{
    public function fromArray(array $arr): BaseEntity;
}
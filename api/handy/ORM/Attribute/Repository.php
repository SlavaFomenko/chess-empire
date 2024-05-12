<?php

namespace Handy\ORM\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Repository
{

    /**
     * @var string
     */
    private string $entity;

    /**
     * @param string $entity
     */
    public function __construct(string $entity)
    {
        $this->entity = $entity;
    }

    /**
     * @return string
     */
    public function getEntity(): string
    {
        return $this->entity;
    }

}
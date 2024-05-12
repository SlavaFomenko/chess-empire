<?php

namespace Handy\ORM\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Entity
{

    /**
     * @var string
     */
    private string $repository;

    /**
     * @var string
     */
    private string $table;

    /**
     * @param string $repository
     * @param string $table
     */
    public function __construct(string $repository, string $table)
    {
        $this->repository = $repository;
        $this->table = $table;
    }

    /**
     * @return string
     */
    public function getRepository(): string
    {
        return $this->repository;
    }

    /**
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

}
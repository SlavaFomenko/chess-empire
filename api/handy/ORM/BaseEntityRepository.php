<?php

namespace Handy\ORM;

use Handy\Context;
use Handy\Exception\AttributeNotFoundException;
use Handy\ORM\Attribute\Entity;
use Handy\ORM\Attribute\Repository;
use ReflectionClass;

class BaseEntityRepository
{

    private EntityManager $entityManager;

    private string $entityClass;

    private string $entityTable;

    public function __construct(EntityManager $em)
    {
        $this->entityManager = $em;

        $repositoryAttribute = (new ReflectionClass($this))->getAttributes(Repository::class);
        $repositoryAttribute = @$repositoryAttribute[0] ?? throw new AttributeNotFoundException("Repository attribute not found in \"" . $this::class . "\"");

        $this->entityClass = $repositoryAttribute->newInstance()->getEntity();

        $entityAttribute = (new ReflectionClass($this->entityClass))->getAttributes(Entity::class);
        $entityAttribute = @$entityAttribute[0] ?? throw new AttributeNotFoundException("Entity attribute not found in \"" . $this::class . "\"");
        $this->entityTable = $entityAttribute->newInstance()->getTable();
    }

    public function find($id): mixed
    {
        $idColumn = (new $this->entityClass())->getIdColumn()["column"];

        return $this->findOneBy([$idColumn => $id]);
    }

    public function findAll(?int $limit = null, ?int $offset = null): array
    {
        return $this->findBy([], $limit, $offset);
    }

    public function findBy(array $criteria, ?int $limit = null, ?int $offset = null): array
    {
        $qb = new QueryBuilder();
        $qb->select()
            ->from($this->entityTable);

        foreach ($criteria as $key => $value) {
            $qb->andWhere($key . " = :" . $key)
                ->setParam([$key => $value]);
        }

        $limit !== null && $qb->limit($limit);
        $offset !== null && $qb->offset($offset);

        $q = $qb->getQuery();

        $entities = Context::$connection->execute($q, $this->entityClass);

        foreach ($entities as $entity) {
            $this->entityManager->track($entity);
        }

        return $entities;
    }

    public function findOneBy(array $criteria): mixed
    {
        $qb = new QueryBuilder();
        $qb->select()
            ->from($this->entityTable)
            ->limit(1);

        foreach ($criteria as $key => $value) {
            $qb->andWhere($key . " = :" . $key)
                ->setParam([$key => $value]);
        }

        $q = $qb->getQuery();

        $entity = Context::$connection->execute($q, $this->entityClass)[0] ?? null;

        if ($entity !== null) {
            $this->entityManager->track($entity);
        }

        return $entity;
    }

}
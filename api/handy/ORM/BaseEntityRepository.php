<?php

namespace Handy\ORM;

use Handy\Context;
use Handy\Exception\AttributeNotFoundException;
use Handy\ORM\Attribute\Entity;
use Handy\ORM\Attribute\Repository;
use ReflectionClass;

class BaseEntityRepository
{

    protected EntityManager $entityManager;

    protected string $entityClass;

    protected string $entityTable;

    public function __construct(EntityManager $em)
    {
        $this->entityManager = $em;

        $repositoryAttribute = (new ReflectionClass($this))->getAttributes(Repository::class);
        $repositoryAttribute = @$repositoryAttribute[0] ?? throw new AttributeNotFoundException("Repository attribute not found in \"" . $this::class . "\"");

        $this->entityClass = $repositoryAttribute->newInstance()->getEntity();

        $entityAttribute = (new ReflectionClass($this->entityClass))->getAttributes(Entity::class);
        $entityAttribute = @$entityAttribute[0] ?? throw new AttributeNotFoundException("Entity attribute not found in \"" . $this->entityClass . "\"");
        $this->entityTable = $entityAttribute->newInstance()->getTable();
    }

    public function find($id): mixed
    {
        $idColumn = (new $this->entityClass())->getIdColumn()["column"];

        return $this->findOneBy([$idColumn => $id]);
    }

    public function findAll(?int $limit = null, ?int $offset = null, array $orderBy = []): array
    {
        return $this->findBy([], false, $limit, $offset, $orderBy);
    }

    public function findBy(array $criteria, bool $or = false, ?int $limit = null, ?int $offset = null, array $orderBy = []): array
    {
        $qb = new QueryBuilder();
        $qb->select()
            ->from($this->entityTable);

        foreach ($criteria as $key => $value) {
            if($or){
                $qb->orWhere($key . " = :" . $key);
            } else {
                $qb->andWhere($key . " = :" . $key);
            }
            $qb->setParam([$key => $value]);
        }

        $limit !== null && $qb->limit($limit);
        $offset !== null && $qb->offset($offset);

        $qb->orderBy($orderBy);

        $q = $qb->getQuery();

        $entities = Context::$connection->execute($q, $this->entityClass);

        foreach ($entities as $entity) {
            $this->entityManager->track($entity);
        }

        return $entities;
    }

    public function findOneBy(array $criteria, bool $or = false): mixed
    {
        return @$this->findBy($criteria, $or,1)[0] ?? null;
    }

}
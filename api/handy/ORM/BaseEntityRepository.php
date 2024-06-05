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

    public function countBy(array $criteria, bool $or = false): int
    {
        $idColumn = (new $this->entityClass())->getIdColumn()["column"];

        $qb = new QueryBuilder();
        $qb->select(["COUNT($idColumn)"])
            ->from($this->entityTable);

        $this->addCriteria($qb, $criteria, $or);

        return (int)Context::$connection->execute($qb->getQuery())[0][0];
    }

    public function addCriteria(QueryBuilder $qb, array $criteria = [], bool $or = false): void
    {
        foreach ($criteria as $key => $value) {
            $operator = "=";
            if(str_starts_with($value, "LIKE")){
                $operator = "LIKE";
                $value = str_replace("LIKE ", "", $value) . "%";
            }
            $condition = $key . " " . $operator . " :" . $key;
            if($or){
                $qb->orWhere($condition);
            } else {
                $qb->andWhere($condition);
            }
            $qb->setParam([$key => $value]);
        }
    }

    public function limitAndOffset(QueryBuilder $qb, ?int $limit = null, ?int $offset = null): void
    {
        $limit !== null && $qb->limit($limit);
        $offset !== null && $qb->offset($offset);
    }

    public function findBy(array $criteria, bool $or = false, ?int $limit = null, ?int $offset = null, array $orderBy = []): array
    {
        $qb = new QueryBuilder();
        $qb->select()
            ->from($this->entityTable);

        $this->addCriteria($qb, $criteria, $or);

        $this->limitAndOffset($qb, $limit, $offset);

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
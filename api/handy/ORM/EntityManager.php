<?php

namespace Handy\ORM;

use Handy\Context;
use Handy\Exception\AttributeNotFoundException;
use Handy\ORM\Attribute\Entity;
use Handy\ORM\Exception\InvalidEntityClassException;
use Handy\ORM\Exception\InvalidRecordIdException;
use Handy\ORM\Exception\InvalidRepositoryClassException;
use ReflectionClass;

class EntityManager
{

    public const STATE_NOT_EXISTS    = ["NOT EXISTS"];
    public const STATE_NOT_PERSISTED = ["NOT PERSISTED"];
    public const STATE_DEFAULTS      = [
        "before" => self::STATE_NOT_EXISTS,
        "after"  => self::STATE_NOT_PERSISTED
    ];

    public array $states;

    public function __construct()
    {
        $this->states = [];
    }

    public function clearStates(): void
    {
        $this->states = [];
    }

    public function getRepository(string $entity): BaseEntityRepository
    {
        if (!is_a($entity, BaseEntity::class, true)) {
            throw new InvalidEntityClassException($entity . " is not inherited from " . BaseEntity::class);
        }

        $entityAttribute = (new ReflectionClass($entity))->getAttributes(Entity::class);
        $entityAttribute = @$entityAttribute[0] ?? throw new AttributeNotFoundException("Entity attribute not found in \"" . $this::class . "\"");

        $repositoryClass = $entityAttribute->newInstance()->getRepository();

        if (!is_a($repositoryClass, BaseEntityRepository::class, true)) {
            throw new InvalidRepositoryClassException($repositoryClass . " is not inherited from" . BaseEntityRepository::class);
        }

        return new $repositoryClass($this);
    }

    public function track(BaseEntity $entity): void
    {
        $key = $this->getRecordId($entity);

        $this->states[$key] = array_merge(
            self::STATE_DEFAULTS,
            [
                "before" => $entity->getStateSnapshot(),
                "after"  => self::STATE_NOT_PERSISTED
            ]
        );
    }

    public function persist(BaseEntity $entity): void
    {
        $key = $this->getRecordId($entity);

        $this->states[$key] = array_merge(
            $this->states[$key] ?? self::STATE_DEFAULTS,
            [
                "after" => $entity->getStateSnapshot()
            ]
        );
    }

    public function remove(BaseEntity $entity): void
    {
        $key = $this->getRecordId($entity);

        $this->states[$key] = array_merge(
            $this->states[$key] ?? self::STATE_DEFAULTS,
            [
                "after" => self::STATE_NOT_EXISTS
            ]
        );
    }

    public function flush(): void
    {
        foreach ($this->states as $key => $state) {
            $query = $this->getQueryForStateSet([$key => $state]);
            if ($query === null) {
                continue;
            }
            Context::$connection->execute($query);
        }

        $this->clearStates();
    }

    public function getQueryForStateSet(array $stateSet): ?Query
    {
        $states = array_values($stateSet)[0];
        if ($states["before"] == self::STATE_NOT_EXISTS && $this->isValidState($states["after"])) {
            return $this->getInsertQuery($stateSet);
        } else if ($this->isValidState($states["before"]) && $states["after"] == self::STATE_NOT_EXISTS) {
            return $this->getDeleteQuery($stateSet);
        } else if ($this->isValidState($states["before"]) && $this->isValidState($states["after"])) {
            if (empty(BaseEntity::getStateDifference($states["before"], $states["after"]))) {
                return null;
            }
            return $this->getUpdateQuery($stateSet);
        }

        return null;
    }

    public function getDeleteQuery(array $stateSet): Query
    {
        $entityData = $this->parseRecordId(array_keys($stateSet)[0]);
        $states = array_values($stateSet)[0];

        $qb = new QueryBuilder();
        $qb->deleteFrom($entityData["table"])
            ->where($entityData["id-column"] . " = :" . $entityData["id-column"])
            ->setParam([$entityData["id-column"] => $entityData["id-value"]]);

        return $qb->getQuery();
    }

    public function getInsertQuery(array $stateSet): Query
    {
        $entityData = $this->parseRecordId(array_keys($stateSet)[0]);
        $states = array_values($stateSet)[0];

        $qb = new QueryBuilder();
        $qb->insertInto($entityData["table"])
            ->values($states["after"]);

        return $qb->getQuery();
    }

    public function getUpdateQuery(array $stateSet): Query
    {
        $entityData = $this->parseRecordId(array_keys($stateSet)[0]);
        $states = array_values($stateSet)[0];

        $changes = BaseEntity::getStateDifference($states["before"], $states["after"]);

        $qb = new QueryBuilder();
        $qb->update($entityData["table"])
            ->values($changes)
            ->where($entityData["id-column"] . " = :" . $entityData["id-column"])
            ->setParam([$entityData["id-column"] => $entityData["id-value"]]);

        return $qb->getQuery();
    }

    public function isValidState(array $state): bool
    {
        return !in_array($state, [
            self::STATE_NOT_EXISTS,
            self::STATE_NOT_PERSISTED
        ]);
    }

    public function getRecordId(BaseEntity $entity): string
    {
        return $entity::class . "@" . ($entity->getIdColumn()["value"] ?? uniqid("NEW_ENTITY_"));
    }

    public function parseRecordId(string $id): array
    {
        $parts = explode("@", $id, 2);
        $entityClass = $parts[0];
        $value = @$parts[1] ?? throw new InvalidRecordIdException("Record id \"" . $id . "\" id not valid");

        return [
            "class"     => $entityClass,
            "table"     => BaseEntity::getEntityTable($entityClass),
            "id-column" => (new $entityClass())->getIdColumn()["column"],
            "id-value"  => $parts[1]
        ];
    }

}
<?php

namespace Handy\ORM;

use Handy\Exception\AttributeNotFoundException;
use Handy\ORM\Attribute\Column;
use Handy\ORM\Attribute\Entity;
use Handy\ORM\Attribute\Id;
use Handy\ORM\Exception\IdNotFoundException;
use Handy\Utils\Resolver;
use ReflectionClass;

class BaseEntity
{

    //For lazy loading
    private ?EntityManager $entityManager = null;

    public function fromQueryResult(array $data): void
    {
        $props = Resolver::getPropsInClass($this::class, [Column::class]);

        foreach ($props as $prop) {
            $columnAttribute = $prop->getAttributes(Column::class)[0]->newInstance();

            $parser = $columnAttribute->getType()->sqlToPhp();

            @$prop->setValue($this, $parser($data[$columnAttribute->getColumn()]));
        }
    }

    public function getStateSnapshot(): array
    {
        $stateSnapshot = [];

        $props = Resolver::getPropsInClass($this::class, [Column::class]);

        foreach ($props as $prop) {
            $columnAttribute = $prop->getAttributes(Column::class)[0]->newInstance();

            $stateSnapshot[$columnAttribute->getColumn()] = $prop->getValue($this);
        }

        return $stateSnapshot;
    }

    public static function getStateDifference(array $a, array $b): array
    {
        return @array_filter($b, fn($key) => $a[$key] !== $b[$key], ARRAY_FILTER_USE_KEY);
    }

    public function getIdColumn(): array
    {
        $props = Resolver::getPropsInClass($this::class, [
            Column::class,
            Id::class
        ]);

        if (empty($props)) {
            throw new IdNotFoundException("Id column not found in " . $this::class);
        }

        /** @var Column $columnAttribute */
        $columnAttribute = $props[0]->getAttributes(Column::class)[0]->newInstance();

        return [
            "column" => $columnAttribute->getColumn(),
            "value"  => $props[0]->getValue($this)
        ];
    }

    public static function getEntityTable(string $entityClass): string
    {
        $entityAttribute = (new ReflectionClass($entityClass))->getAttributes(Entity::class);
        $entityAttribute = @$entityAttribute[0] ?? throw new AttributeNotFoundException("Entity attribute not found in \"" . $entityClass . "\"");;

        return $entityAttribute->newInstance()->getTable();
    }

}
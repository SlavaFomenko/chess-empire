<?php

namespace Handy\ORM;

use Handy\ORM\Attribute\Column;
use Handy\ORM\Attribute\Id;
use ReflectionClass;

class BaseEntity
{

    public function fromQueryResult(array $data): void
    {
        $reflectionCLass = new ReflectionClass($this::class);
        $props = $reflectionCLass->getProperties();

        foreach ($props as $prop) {
            $columnAttribute = $prop->getAttributes(Column::class);

            if (empty($columnAttribute)) {
                continue;
            }

            /** @var Column $columnAttribute */
            $columnAttribute = $columnAttribute[0]->newInstance();

            $parser = $columnAttribute->getType()->sqlToPhp();

            @$prop->setValue($this, $parser($data[$columnAttribute->getColumn()]));
        }
    }

}
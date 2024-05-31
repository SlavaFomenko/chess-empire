<?php

namespace App\Repository;

use App\Entity\Game;
use Handy\Context;
use Handy\ORM\Attribute\Repository;
use Handy\ORM\BaseEntityRepository;
use Handy\ORM\QueryBuilder;

#[Repository(entity: Game::class)]
class GameRepository extends BaseEntityRepository
{

    public function findByUserName(string $name, ?int $limit = null, ?int $offset = null, array $orderBy = []): array
    {
        $qb = new QueryBuilder();
        $qb->select(Game::class)
            ->from("game")
            ->join("user", "u")
            ->on("game.b_id = u.id")
            ->orOn("game.w_id = u.id")
            ->where("u.username LIKE :name1")
            ->orWhere("u.first_name LIKE :name2")
            ->orWhere("u.last_name LIKE :name3")
            ->setParam([
                "name1" => $name . "%",
                "name2" => $name . "%",
                "name3" => $name . "%"
            ]);

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

}
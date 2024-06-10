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

    public function findByNameAndDate(string $name, int $limit, int $offset, array $orderBy = [], ?int $startDate = null, ?int $endDate = null)
    {
        $qb = new QueryBuilder();

        $qb->select($this->entityClass)
            ->from("game")
            ->join("user", "u")
            ->on("game.black_id = u.id")
            ->orOn("game.white_id = u.id")
            ->where("(u.username LIKE :name1 OR u.first_name LIKE :name2 OR u.last_name LIKE :name3)")
            ->setParam([
                "name1" => $name . "%",
                "name2" => $name . "%",
                "name3" => $name . "%"
            ]);

        if ($startDate) {
            $qb->andWhere('game.played_date >= :startDate')
                ->setParam(['startDate' => $startDate]);
        }

        if ($endDate) {
            $qb->andWhere('game.played_date <= :endDate')
                ->setParam(['endDate' => $endDate]);
        }

        $qb->orderBy($orderBy);

        $qb->offset($offset)
            ->limit($limit);

        $entities = Context::$connection->execute($qb->getQuery(), $this->entityClass);

        foreach ($entities as $entity) {
            $this->entityManager->track($entity);
        }

        return $entities;
    }

    public function countByNameAndDate(string $name, ?int $startDate = null, ?int $endDate = null)
    {
        $idColumn = (new $this->entityClass())->getIdColumn()["column"];
        $qb = new QueryBuilder();

        $qb->select(["COUNT(game.$idColumn)"])
            ->from("game")
            ->join("user", "u")
            ->on("game.black_id = u.id")
            ->orOn("game.white_id = u.id")
            ->where("(u.username LIKE :name1 OR u.first_name LIKE :name2 OR u.last_name LIKE :name3)")
            ->setParam([
                "name1" => $name . "%",
                "name2" => $name . "%",
                "name3" => $name . "%"
            ]);

        if ($startDate) {
            $qb->andWhere('game.played_date >= :startDate')
                ->setParam(['startDate' => $startDate]);
        }

        if ($endDate) {
            $qb->andWhere('game.played_date <= :endDate')
                ->setParam(['endDate' => $endDate]);
        }

        $q = $qb->getQuery();

        return (int)Context::$connection->execute($qb->getQuery())[0][0];
    }

    public function findByUserName(string $name, ?int $limit = null, ?int $offset = null, array $orderBy = []): array
    {
        $qb = new QueryBuilder();
        $qb->select(Game::class)
            ->from("game")
            ->join("user", "u")
            ->on("game.black_id = u.id")
            ->orOn("game.white_id = u.id")
            ->where("u.username LIKE :name1")
            ->orWhere("u.first_name LIKE :name2")
            ->orWhere("u.last_name LIKE :name3")
            ->setParam([
                "name1" => $name . "%",
                "name2" => $name . "%",
                "name3" => $name . "%"
            ]);

        $this->limitAndOffset($qb, $limit, $offset);

        $qb->orderBy($orderBy);

        $q = $qb->getQuery();

        $entities = Context::$connection->execute($q, $this->entityClass);

        foreach ($entities as $entity) {
            $this->entityManager->track($entity);
        }

        return $entities;
    }

    public function countByUserName(string $name): int
    {
        $idColumn = (new $this->entityClass())->getIdColumn()["column"];
        $qb = new QueryBuilder();
        $qb->select(["COUNT(game.$idColumn)"])
            ->from("game")
            ->join("user", "u")
            ->on("game.black_id = u.id")
            ->orOn("game.white_id = u.id")
            ->where("u.username LIKE :name1")
            ->orWhere("u.first_name LIKE :name2")
            ->orWhere("u.last_name LIKE :name3")
            ->setParam([
                "name1" => $name . "%",
                "name2" => $name . "%",
                "name3" => $name . "%"
            ]);

        $q = $qb->getQuery();

        return (int)Context::$connection->execute($qb->getQuery())[0][0];
    }

}
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

    public function findGameByDate(string $userName, int $limit, int $offset, array $orderBy = [], $startDate = null, ?\DateTime $endDate = null)
    {
        $qb = new QueryBuilder();

        $qb->select('g')
            ->from('games', 'g')
            ->join('users', 'b')
            ->on('g.black_id = b.id')
            ->join('users', 'w')
            ->on('g.white_id = w.id')
            ->where('(b.userName LIKE :userName OR w.userName LIKE :userName)')
            ->setParam(['userName' => '%' . $userName . '%']);

        if ($startDate) {
            $qb->andWhere('g.played_date >= :startDate')
                ->setParam(['startDate' => $startDate]);
        }

        if ($endDate) {
            $qb->andWhere('g.played_date <= :endDate')
                ->setParam(['endDate' => $endDate]);
        }

        foreach ($orderBy as [$column, $direction]) {
            $qb->orderBy([$column => $direction]);
        }

        $qb->offset($offset)
            ->limit($limit);

        return $qb->getQuery()->getResult();
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
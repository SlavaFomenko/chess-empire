<?php

namespace App\Repository;

use App\Entity\Game;
use Handy\ORM\Attribute\Repository;
use Handy\ORM\BaseEntityRepository;

#[Repository(entity: Game::class)]
class GameRepository extends BaseEntityRepository
{

}
<?php

namespace App\Repository;

use App\Entity\FriendPair;
use Handy\ORM\Attribute\Repository;
use Handy\ORM\BaseEntityRepository;

#[Repository(entity: FriendPair::class)]
class FriendPairRepository extends BaseEntityRepository
{

}
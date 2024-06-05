<?php

namespace App\Repository;

use App\Entity\RatingRange;
use Handy\ORM\Attribute\Repository;
use Handy\ORM\BaseEntityRepository;

#[Repository(entity: RatingRange::class)]
class RatingRangeRepository extends BaseEntityRepository
{

}
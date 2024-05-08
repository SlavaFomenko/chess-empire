<?php

namespace App\Repository;

use App\Entity\User;
use Handy\ORM\Attribute\Repository;
use Handy\ORM\BaseEntityRepository;

#[Repository(entity: User::class)]
class UserRepository extends BaseEntityRepository
{

}
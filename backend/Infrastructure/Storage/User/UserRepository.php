<?php

namespace PlayOrPay\Infrastructure\Storage\User;

use PlayOrPay\Domain\User\User;
use PlayOrPay\Infrastructure\Storage\Doctrine\Repository\ServiceEntityRepository;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method User      get($id, $lockMode = null, $lockVersion = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function getEntityClass(): string
    {
        return User::class;
    }
}

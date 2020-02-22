<?php

namespace PlayOrPay\Infrastructure\Storage\User;

use PlayOrPay\Domain\Role\Role;
use PlayOrPay\Infrastructure\Storage\Doctrine\Repository\ServiceEntityRepository;

/**
 * @method Role|null find($id, $lockMode = null, $lockVersion = null)
 */
class RoleRepository extends ServiceEntityRepository
{
    public function getEntityClass(): string
    {
        return Role::class;
    }
}

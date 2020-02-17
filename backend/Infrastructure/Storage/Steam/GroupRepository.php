<?php

namespace PlayOrPay\Infrastructure\Storage\Steam;

use PlayOrPay\Domain\Steam\Group;
use PlayOrPay\Infrastructure\Storage\Doctrine\Repository\ServiceEntityRepository;

/**
 * @method Group find($id, $lockMode = null, $lockVersion = null)
 * @method void save(Group $entity)
 * @method Group get($id, $lockMode = null, $lockVersion = null) : object
 */
class GroupRepository extends ServiceEntityRepository
{
    public function isSaveAllowed(): bool
    {
        return true;
    }

    public function getEntityClass(): string
    {
        return Group::class;
    }
}

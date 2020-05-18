<?php

namespace PlayOrPay\Infrastructure\Storage\Steam;

use Assert\Assert;
use PlayOrPay\Domain\Steam\Group;
use PlayOrPay\Infrastructure\Storage\Doctrine\Repository\ServiceEntityRepository;

/**
 * @method Group|null find($id, $lockMode = null, $lockVersion = null)
 * @method void  save(Group $entity)
 * @method Group get($id, $lockMode = null, $lockVersion = null)  : object
 */
class GroupRepository extends ServiceEntityRepository
{
    public function getEntityClass(): string
    {
        return Group::class;
    }

    /**
     * @param string[] $codes
     *
     * @return Group[]
     */
    public function findByCodes(array $codes): array
    {
        Assert::thatAll($codes)->string()->minLength(1);

        return $this->findBy(['code' => $codes]);
    }
}

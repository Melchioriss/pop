<?php

namespace PlayOrPay\Infrastructure\Storage\Event;

use PlayOrPay\Domain\Event\EventReward;
use PlayOrPay\Infrastructure\Storage\Doctrine\Repository\ServiceEntityRepository;

/**
 * @method EventReward get($id, $lockMode = null, $lockVersion = null)
 */
class EventRewardRepository extends ServiceEntityRepository
{
    public function getEntityClass(): string
    {
        return EventReward::class;
    }
}

<?php

namespace PlayOrPay\Infrastructure\Storage\Event;

use PlayOrPay\Domain\Event\EventPick;
use PlayOrPay\Infrastructure\Storage\Doctrine\Repository\ServiceEntityRepository;

/**
 * @method EventPick get($id, $lockMode = null, $lockVersion = null)
 */
class EventPickRepository extends ServiceEntityRepository
{
    public function getEntityClass(): string
    {
        return EventPick::class;
    }
}

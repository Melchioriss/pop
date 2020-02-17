<?php

namespace PlayOrPay\Infrastructure\Storage\Event;

use PlayOrPay\Domain\Event\Event;
use PlayOrPay\Infrastructure\Storage\Doctrine\Repository\ServiceEntityRepository;

/**
 * @method Event get($id, $lockMode = null, $lockVersion = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public function isSaveAllowed(): bool
    {
        return true;
    }

    public function getEntityClass(): string
    {
        return Event::class;
    }
}

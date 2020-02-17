<?php

namespace PlayOrPay\Infrastructure\Storage\Event;

use PlayOrPay\Domain\Event\EventParticipant;
use PlayOrPay\Infrastructure\Storage\Doctrine\Repository\ServiceEntityRepository;

/**
 * @method EventParticipant get($id, $lockMode = null, $lockVersion = null)
 */
class EventParticipantRepository extends ServiceEntityRepository
{
    public function getEntityClass(): string
    {
        return EventParticipant::class;
    }
}

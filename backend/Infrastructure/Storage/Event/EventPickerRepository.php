<?php

namespace PlayOrPay\Infrastructure\Storage\Event;

use PlayOrPay\Domain\Event\EventPicker;
use PlayOrPay\Infrastructure\Storage\Doctrine\Repository\ServiceEntityRepository;
use Ramsey\Uuid\UuidInterface;

/**
 * @method EventPicker get(UuidInterface $id, $lockMode = null, $lockVersion = null)
 */
class EventPickerRepository extends ServiceEntityRepository
{
    public function getEntityClass(): string
    {
        return EventPicker::class;
    }
}

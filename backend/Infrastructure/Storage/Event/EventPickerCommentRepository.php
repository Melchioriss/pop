<?php

namespace PlayOrPay\Infrastructure\Storage\Event;

use PlayOrPay\Domain\Event\EventPickerComment;
use PlayOrPay\Infrastructure\Storage\Doctrine\Repository\ServiceEntityRepository;

/**
 * @method EventPickerComment get($id, $lockMode = null, $lockVersion = null)
 */
class EventPickerCommentRepository extends ServiceEntityRepository
{
    public function getEntityClass(): string
    {
        return EventPickerComment::class;
    }
}

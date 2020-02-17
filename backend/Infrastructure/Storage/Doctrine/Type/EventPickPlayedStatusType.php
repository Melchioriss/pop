<?php

namespace PlayOrPay\Infrastructure\Storage\Doctrine\Type;

use PlayOrPay\Domain\Event\EventPickPlayedStatus;

class EventPickPlayedStatusType extends IntegerEnumType
{
    public function getEnumClass(): string
    {
        return EventPickPlayedStatus::class;
    }
}

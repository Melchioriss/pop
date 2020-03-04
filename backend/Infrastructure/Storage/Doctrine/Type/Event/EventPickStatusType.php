<?php

namespace PlayOrPay\Infrastructure\Storage\Doctrine\Type\Event;

use PlayOrPay\Domain\Event\EventPickStatus;
use PlayOrPay\Infrastructure\Storage\Doctrine\Type\IntegerEnumType;

class EventPickStatusType extends IntegerEnumType
{
    public function getEnumClass(): string
    {
        return EventPickStatus::class;
    }
}

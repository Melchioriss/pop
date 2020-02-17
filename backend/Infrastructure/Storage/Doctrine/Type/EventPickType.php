<?php

namespace PlayOrPay\Infrastructure\Storage\Doctrine\Type;

use PlayOrPay\Domain\Event\EventPickType as EventPickTypeEnum;

class EventPickType extends IntegerEnumType
{
    public function getEnumClass(): string
    {
        return EventPickTypeEnum::class;
    }
}

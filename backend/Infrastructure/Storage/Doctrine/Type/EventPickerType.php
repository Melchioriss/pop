<?php

namespace PlayOrPay\Infrastructure\Storage\Doctrine\Type;

use PlayOrPay\Domain\Event\EventPickerType as EventPickerTypeEnum;

class EventPickerType extends IntegerEnumType
{
    public function getEnumClass(): string
    {
        return EventPickerTypeEnum::class;
    }
}

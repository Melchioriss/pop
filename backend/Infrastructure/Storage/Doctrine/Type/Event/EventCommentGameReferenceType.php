<?php

namespace PlayOrPay\Infrastructure\Storage\Doctrine\Type\Event;

use PlayOrPay\Domain\Event\EventCommentGameReferenceType as EventCommentGameReferenceTypeEnum;
use PlayOrPay\Infrastructure\Storage\Doctrine\Type\IntegerEnumType;

class EventCommentGameReferenceType extends IntegerEnumType
{
    public function getEnumClass(): string
    {
        return EventCommentGameReferenceTypeEnum::class;
    }
}

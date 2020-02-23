<?php

namespace PlayOrPay\Infrastructure\Storage\Doctrine\Type\Event;

use PlayOrPay\Domain\Event\RewardReason;
use PlayOrPay\Infrastructure\Storage\Doctrine\Type\IntegerEnumType;

class RewardReasonType extends IntegerEnumType
{
    public function getEnumClass(): string
    {
        return RewardReason::class;
    }
}

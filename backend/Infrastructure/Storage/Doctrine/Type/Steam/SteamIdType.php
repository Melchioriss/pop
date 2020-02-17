<?php

namespace PlayOrPay\Infrastructure\Storage\Doctrine\Type\Steam;

use PlayOrPay\Domain\Steam\SteamId;
use PlayOrPay\Infrastructure\Storage\Doctrine\Type\BigIntObjectValueType;

class SteamIdType extends BigIntObjectValueType
{
    public function getClass(): string
    {
        return SteamId::class;
    }
}

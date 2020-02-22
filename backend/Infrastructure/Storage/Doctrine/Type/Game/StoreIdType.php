<?php

namespace PlayOrPay\Infrastructure\Storage\Doctrine\Type\Game;

use PlayOrPay\Domain\Game\StoreId;
use PlayOrPay\Infrastructure\Storage\Doctrine\Type\IntegerEnumType;

class StoreIdType extends IntegerEnumType
{
    public function getEnumClass(): string
    {
        return StoreId::class;
    }
}

<?php

namespace PlayOrPay\Infrastructure\Storage\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\IntegerType;

abstract class IntegerEnumType extends IntegerType
{
    use EnumTypeTrait;

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?int
    {
        if ($value === null) {
            return null;
        }

        return (int) (string) $value;
    }
}

<?php

namespace PlayOrPay\Infrastructure\Storage\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\IntegerType;

abstract class IntegerEnumType extends IntegerType
{
    use EnumTypeTrait;

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return (int) (string) $value;
    }
}

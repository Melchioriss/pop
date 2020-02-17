<?php

namespace PlayOrPay\Infrastructure\Storage\Doctrine\Type;

use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class BigIntType extends \Doctrine\DBAL\Types\BigIntType
{
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return $value === null ? null : (int)$value;
    }

    public function getBindingType()
    {
        return ParameterType::INTEGER;
    }
}

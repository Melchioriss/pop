<?php

namespace PlayOrPay\Infrastructure\Storage\Doctrine\Type;

use Doctrine\DBAL\Types\IntegerType;
use PlayOrPay\Infrastructure\Storage\ObjectValueTypeInterface;

abstract class BigIntObjectValueType extends BigIntType implements ObjectValueTypeInterface
{
    use ObjectValueTypeTrait;
}

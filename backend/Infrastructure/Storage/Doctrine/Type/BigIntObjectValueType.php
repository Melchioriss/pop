<?php

namespace PlayOrPay\Infrastructure\Storage\Doctrine\Type;

use PlayOrPay\Infrastructure\Storage\ObjectValueTypeInterface;

abstract class BigIntObjectValueType extends BigIntType implements ObjectValueTypeInterface
{
    use ObjectValueTypeTrait;
}

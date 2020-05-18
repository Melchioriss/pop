<?php

namespace PlayOrPay\Infrastructure\Storage\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use ReflectionClass;

trait EnumTypeTrait
{
    abstract public function getEnumClass(): string;

    public function convertToPHPValue(
        $value,
        /* @noinspection PhpUnusedParameterInspection */
        AbstractPlatform $platform
    ) {
        if ($value === null) {
            return null;
        }

        $enumClass = $this->getEnumClass();

        return new $enumClass($value);
    }

    public function getName(): string
    {
        return preg_replace('/Type$/', '', (new ReflectionClass(static::class))->getShortName());
    }

    public function requiresSQLCommentHint(/* @noinspection PhpUnusedParameterInspection */ AbstractPlatform $platform): bool
    {
        return true;
    }
}

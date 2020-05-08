<?php

namespace PlayOrPay\Infrastructure\Storage\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Ducks\Component\SplTypes\SplType;

trait ObjectValueTypeTrait
{
    abstract public function getClass(): string;

    /**
     * @param string $value
     * @param AbstractPlatform $platform
     *
     * @throws ConversionException
     *
     * @return object|null
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        $class = $this->getClass();
        if (!is_a($class, SplType::class, true)) {
            throw ConversionException::conversionFailedUnserialization($class, 'must be SplType');
        }

        $innerValue = parent::convertToPHPValue(
            $value,
            $platform
        );

        return $innerValue === null ? null : new $class($innerValue);
    }

    /**
     * @param mixed            $value
     * @param AbstractPlatform $platform
     *
     * @throws ConversionException
     *
     * @return mixed
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (!is_object($value)) {
            return parent::convertToDatabaseValue($value, $platform);
        }

        if (!$value instanceof SplType) {
            throw ConversionException::conversionFailedUnserialization(get_class($value), 'must be SplType');
        }

        // @noinspection PhpUndefinedClassInspection
        return parent::convertToDatabaseValue($value->__default, $platform);
    }
}

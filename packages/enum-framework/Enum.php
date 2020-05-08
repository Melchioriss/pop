<?php

namespace Insideone\Package\EnumFramework;

use Assert\Assert;
use Ducks\Component\SplTypes\SplEnum;
use ReflectionClass;
use ReflectionException;
use UnexpectedValueException;

class Enum extends SplEnum
{
    public function __construct($value, $strict = null)
    {
        Assert::that($value)->notNull();
        parent::__construct($value, $strict);
    }

    /**
     * @param bool $getFirstOnAmbiguous
     *
     * @throws ReflectionException
     * @throws AmbiguousValueException
     *
     * @return string
     */
    public function getCodename(bool $getFirstOnAmbiguous = false): string
    {
        $class = new ReflectionClass($this);
        $codenames = array_keys($class->getConstants(), $this->__default);
        if (!$codenames) {
            throw new UnexpectedValueException(sprintf("Codename wasn't found from the inner value '%s'", $this->__default));
        }

        if (!$getFirstOnAmbiguous && count($codenames) > 1) {
            throw new AmbiguousValueException(sprintf("Value '%s' corresponds to multiple codenames: %s", $this->__default, implode(', ', $codenames)));
        }

        return $codenames[0];
    }

    /**
     * @param Enum|int|string $anotherEnum
     *
     * @return bool
     */
    public function equalTo($anotherEnum): bool
    {
        if (!is_object($anotherEnum)) {
            $anotherEnum = new static($anotherEnum);
        }

        if (get_class($this) !== get_class($anotherEnum)) {
            return false;
        }

        return (string)$this->__default === (string)$anotherEnum->__default;
    }

    /**
     * @param Enum|int|string|Enum[]|int[]|string[] ...$anotherEnums
     *
     * @return bool
     */
    public function equalToOneOf(...$anotherEnums): bool
    {
        if (count($anotherEnums) === 1 && is_array($anotherEnums[0])) {
            $anotherEnums = $anotherEnums[0];
        }

        foreach ($anotherEnums as $anotherEnum) {
            if ($this->equalTo($anotherEnum)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param bool $includeDefault
     *
     * @return array<string, int>|array<string, string>
     *
     * @throws ReflectionException
     */
    public static function getOptions(bool $includeDefault = false): array
    {
        $constants = (new ReflectionClass(static::class))->getConstants();
        if (!$includeDefault) {
            unset($constants['__default']);
        }
        return $constants;
    }

    /**
     * @param bool $includeDefault
     *
     * @return static[]
     *
     * @throws ReflectionException
     */
    public static function getEnums(bool $includeDefault = false): array
    {
        $enums = [];
        foreach (array_values(self::getOptions($includeDefault)) as $option) {
            $enums[] = new static($option);
        }

        return $enums;
    }
}

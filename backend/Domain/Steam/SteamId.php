<?php

namespace PlayOrPay\Domain\Steam;

use Assert\Assert;
use Ducks\Component\SplTypes\SplInt;

/**
 * @property int $__default
 */
class SteamId extends SplInt
{
    public function __construct($value, $strict = null)
    {
        // TODO: make better check
        Assert::that($value)->min(1, 'Wrong steam identity');

        parent::__construct($value, $strict);
    }
}

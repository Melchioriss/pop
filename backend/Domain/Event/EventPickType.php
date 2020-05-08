<?php

namespace PlayOrPay\Domain\Event;

use Insideone\Package\EnumFramework\Enum;

class EventPickType extends Enum
{
    const SHORT = 10;
    const MEDIUM = 20;
    const LONG = 30;
    const VERY_LONG = 40;
}

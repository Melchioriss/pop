<?php

namespace PlayOrPay\Domain\Event;

use Insideone\Package\EnumFramework\Enum;

class EventPickStatus extends Enum
{
    const ACTIVE = 10;
    const REJECTED = 20;
}

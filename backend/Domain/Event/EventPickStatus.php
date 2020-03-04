<?php

namespace PlayOrPay\Domain\Event;

use PlayOrPay\Package\EnumFramework\Enum;

class EventPickStatus extends Enum
{
    const ACTIVE = 10;
    const REJECTED = 20;
}

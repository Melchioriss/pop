<?php

namespace PlayOrPay\Domain\Event;

use PlayOrPay\Package\EnumFramework\Enum;

class EventPickPlayedStatus extends Enum
{
    const NOT_PLAYED = 0;

    const UNFINISHED = 10;

    const BEATEN = 20;

    const COMPLETED = 30;

    const ABANDONED = 40;
}

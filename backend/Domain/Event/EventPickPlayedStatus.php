<?php

namespace PlayOrPay\Domain\Event;

use Insideone\Package\EnumFramework\Enum;

class EventPickPlayedStatus extends Enum
{
    const NOT_PLAYED = 5;

    const UNFINISHED = 10;

    const BEATEN = 20;

    const COMPLETED = 30;

    const ABANDONED = 40;
}

<?php

namespace PlayOrPay\Domain\Event;

use Ducks\Component\SplTypes\SplEnum;

class EventPickPlayedStatus extends SplEnum
{
    const NOT_PLAYED = 0;

    const UNFINISHED = 10;

    const BEATEN = 20;

    const COMPLETED = 30;

    const ABANDONED = 40;
}

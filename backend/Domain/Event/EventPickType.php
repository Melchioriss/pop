<?php

namespace PlayOrPay\Domain\Event;

use Ducks\Component\SplTypes\SplEnum;

class EventPickType extends SplEnum
{
    const SHORT = 10;
    const MEDIUM = 20;
    const LONG = 30;
    const VERY_LONG = 40;
}

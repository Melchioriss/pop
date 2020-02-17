<?php

namespace PlayOrPay\Domain\Event;

use Ducks\Component\SplTypes\SplEnum;

class EventPickerType extends SplEnum
{
    const MINOR = 10;
    const MAJOR = 20;
}

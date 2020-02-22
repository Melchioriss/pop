<?php

namespace PlayOrPay\Domain\Event;

use PlayOrPay\Package\EnumFramework\Enum;

class EventPickerType extends Enum
{
    const MINOR = 10;
    const MAJOR = 20;
}

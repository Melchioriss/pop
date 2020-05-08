<?php

namespace PlayOrPay\Domain\Event;

use Insideone\Package\EnumFramework\Enum;

class EventCommentGameReferenceType extends Enum
{
    const REVIEW = 10;
    const REPICK = 20;
}

<?php

namespace PlayOrPay\Application\Schema\DomainEvent\Log;

use PlayOrPay\Domain\Event\EventPickPlayedStatus;
use PlayOrPay\Domain\Event\PlayingState;

class LogPick
{
    /** @var string */
    public $uuid;

    /** @var int */
    public $type;

    /** @var EventPickPlayedStatus */
    public $playedStatus;

    /** @var PlayingState */
    public $playingState;
}

<?php

namespace PlayOrPay\Application\Schema\DomainEvent\Activity;

use PlayOrPay\Domain\Event\EventPickPlayedStatus;
use PlayOrPay\Domain\Event\PlayingState;

class ActivityPick
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

<?php

namespace PlayOrPay\Application\Schema\Event\Event\Detail;

use League\Period\Period;

class DetailEventView
{
    /** @var string */
    public $uuid;

    /** @var string */
    public $name;

    /** @var string */
    public $description;

    /** @var Period */
    public $activePeriod;

    /** @var DetailEventParticipantView[] */
    public $participants;

    /** @var DetailEventUserView */
    public $users;

    /** @var DetailEventReward[] */
    public $rewards;

    /** @var DetailGameView[] */
    public $games;
}

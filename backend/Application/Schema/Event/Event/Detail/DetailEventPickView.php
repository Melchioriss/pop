<?php

namespace PlayOrPay\Application\Schema\Event\Event\Detail;

use PlayOrPay\Domain\Event\PlayingState;
use PlayOrPay\Infrastructure\Storage\Doctrine\Type\EventPickPlayedStatusType;
use PlayOrPay\Infrastructure\Storage\Doctrine\Type\EventPickType;

class DetailEventPickView
{
    /** @var string */
    public $uuid;

    /** @var EventPickType */
    public $type;

    /** @var string */
    public $game;

    /** @var bool */
    public $rejected;

    /** @var EventPickPlayedStatusType */
    public $playedStatus;

    /** @var PlayingState */
    public $playingState;
}

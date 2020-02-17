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

    /** @var DetailGameView */
    public $game;

    /** @var EventPickPlayedStatusType */
    public $playedStatus;

    /** @var PlayingState */
    public $playingState;
}